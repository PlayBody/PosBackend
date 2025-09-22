<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

class Apimenus extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('staff_model');
        $this->load->model('staff_organ_model');
        $this->load->model('menu_model');
        $this->load->model('menu_variation_model');
        $this->load->model('menu_variation_back_model');
        $this->load->model('table_menu_model');
        $this->load->model('table_menu_ticket_model');
        $this->load->model('organ_model');
        $this->load->model('organ_menu_model');
    }

    public function getMenus(){

        $organ_id = $this->input->post('organ_id');
        $is_user_menu = $this->input->post('is_user_menu');

        $cond = [];
        if (!empty($organ_id)){
            $cond['organ_id'] = $organ_id;
        }
        if (!empty($is_user_menu)){
            $cond['is_user_menu'] = $is_user_menu;
        }

        $menus = $this->menu_model->getListByCond($cond);

        $results['menus'] = $menus;

        echo(json_encode($results));
    }

    public function loadOrderMenus(){

    	$organ_id = $this->input->post('organ_id');
        $table_id = $this->input->post('table_id');

    	$results = [];
    	if (empty($organ_id) || empty($table_id)){
    		$results['isLoad'] = false;
    		echo json_encode($results);
    		return;
    	}

    	$settting = $this->organ_model->getFromId($organ_id);

//    	$menu_count = empty($settting['menu_count']) ? 4 : $settting['menu_count'];

    	$menus = $this->menu_model->getMenuList(['organ_id'=>$organ_id]);

        $table_menus = $this->table_menu_model->getMenuListByCond(['table_id'=>$table_id]);

        $results['isLoad'] = true;
        $results['table_menus'] = $table_menus;
        $results['menus'] = $menus;

        echo(json_encode($results));
    }

    public function loadMenuVariations(){

        $menu_id = $this->input->post('menu_id');

        if (empty($menu_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $variations = $this->menu_variation_model->getVariationList(['menu_id'=>$menu_id]);

        $results['isLoad'] = true;
        $results['variations'] = $variations;

        echo(json_encode($results));
    }

    public function registerReserveMenus(){

        $table_id = $this->input->post('table_id');
        $data = $this->input->post('data');//'[{"title":"ボディケア基本的30分コース","price":"2390","quantity":"2","menu_id":"46","variation_id":null,"use_tickets":{"10":"2"}}]';

        $results = [];
        if (empty($table_id)){
            $results['isSave'] = false;
            echo(json_encode($results));
            return;
        }


        $table_menus = $this->table_menu_model->getMenuListByCond(['table_id'=>$table_id]);
        foreach ($table_menus as $item){
            $this->table_menu_ticket_model->delete_force($item['table_menu_id'], 'table_menu_id');
        }
        $this->table_menu_model->delete_force($table_id, 'table_id');

        $data = json_decode($data);

        foreach ($data as $record) {
            $insertData = [];
            $insertData = array(
                'menu_title' => $record->title,
                'menu_price' => $record->price,
                'quantity' => $record->quantity,
                'table_id' => $table_id,
                'visible' => 1,
                'create_date' => date('Y-m-d'),
                'update_date' => date('Y-m-d'),
            );
            if (!empty($record->menu_id)){
                $insertData['menu_id'] = $record->menu_id;
            }
            if (!empty($record->variation_id)){
                $insertData['variation_id'] = $record->variation_id;
            }

            $talbe_menu_id = $insert = $this->table_menu_model->add($insertData);

            foreach ($record->use_tickets as $key=>$val) {
                $insertTicket = [];
                $insertTicket = array(
                    'table_menu_id' => $talbe_menu_id,
                    'ticket_id' => $key,
                    'count' => $val,
                );

                $insert = $this->table_menu_ticket_model->insertRecord($insertTicket);

            }

        }



        echo(json_encode(array('isSave'=>true)));
        exit(0);
    }


    public function loadMenuList(){

        $condition = $this->input->post('condition');
        $cond = json_decode($condition, true);

        $menus = $this->menu_model->getMenuList($cond);

        $results['isLoad'] = true;
        $results['menus'] = $menus;

        echo(json_encode($results));
    }

    public function loadMenuDetail(){

        $menu_id = $this->input->post('menu_id');
        $organ_id = $this->input->post('organ_id');

        $results = [];
        if (empty($menu_id) || empty($organ_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $menu = $this->menu_model->getFromId($menu_id);

        $staffs = $this->staff_organ_model->getStaffsByOrgan($organ_id, 2);

        $variations = $this->menu_variation_model->getVariationList(['menu_id'=>$menu_id]);

        $variation_data = [];
        foreach ($variations as $item){
            $back_list = $this->menu_variation_back_model->getVariationBacks($item['variation_id']);
            $item['backs'] = $back_list;
            $variation_data[] = $item;
        }

        $results['isLoad'] = true;
        $results['menu'] = $menu;
        $results['staffs'] = $staffs;
        $results['variations'] = $variation_data;

        echo(json_encode($results));
    }

    public function saveMenu(){

        $menu_id = $this->input->post('menu_id');
        $company_id = $this->input->post('company_id');


        if (empty($company_id)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }

        $menu = [];
        if (!empty($menu_id)) {
            $menu = $this->menu_model->getFromId($menu_id);
        }

        $menu['company_id'] = $company_id;
        $menu['menu_title'] = $this->input->post('title');
        $menu['menu_detail']  = $this->input->post('detail');
        $menu['menu_price'] = $this->input->post('price');
        $menu['menu_tax'] = $this->input->post('menu_tax');
        $menu['menu_stock'] = $this->input->post('stock');
        $menu['menu_comment'] = $this->input->post('comment');
        $menu['is_user_menu'] = empty($this->input->post('is_user_menu')) ? null : $this->input->post('is_user_menu');
        $menu['is_goods'] = empty($this->input->post('is_goods')) ? 0 : $this->input->post('is_goods');
        $menu['category_id'] = empty($this->input->post('category_id')) ? 0 : $this->input->post('category_id');
        $menu['menu_time'] = empty($this->input->post('menu_time')) ? null : $this->input->post('menu_time');
        $menu['menu_interval'] = empty($this->input->post('menu_interval')) ? null : $this->input->post('menu_interval');

        if (empty($menu_id)){
            $menu['menu_image'] = empty($this->input->post('image')) ? null : $this->input->post('image');
            $menu['sort_no'] = $this->menu_model->getMaxOrder($company_id);
            $menu['visible'] = 1;

            $menu_id = $this->menu_model->InsertRecord($menu);

        }else{
            if (!empty($this->input->post('image'))){
                $menu['menu_image'] = $this->input->post('image');
            }
            $this->menu_model->updateRecord($menu, 'menu_id');
        }

        $menu_organs = $this->input->post('menu_organs');
        $organs = json_decode($menu_organs, true);
        $old_organ_menus = $this->organ_menu_model->getListByCond(['menu_id' => $menu_id]);
        foreach ($old_organ_menus as $item){
                if(empty($organs) || !array_search($item['organ_id'], $organs) && $organs[0] != $item['organ_id']){
                    $this->organ_menu_model->delete_force($item['id'], 'id');
                }
        }

        foreach ($organs as $organ_id){
            if(empty($old_organ_menus) || !array_search($organ_id, array_column($old_organ_menus, 'organ_id')) && $old_organ_menus[0]['organ_id'] != $organ_id){
                $organ_menu_data = array(
                    'organ_id' => $organ_id,
                    'menu_id' => $menu_id
                );
                $this->organ_menu_model->insertRecord($organ_menu_data);
            }
        }

        $results['isSave'] = true;
        $results['select_menu_id'] = $menu_id;
        echo(json_encode($results));
    }

    public function deleteMenu(){
        $menu_id = $this->input->post('menu_id');

        $results = [];
        if (empty($menu_id)){
            $results['isDelete'] = false;
            echo json_encode($results);
            return;
        }
        $this->menu_model->delete_force($menu_id, 'menu_id');
        $this->menu_variation_model->delete_force($menu_id, 'menu_id');
        $results['isDelete'] = true;

        echo(json_encode($results));
    }

    public function loadMenuVariationRecord(){

        $variation_id = $this->input->post('variation_id');

        $results = [];
        if (empty($variation_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $variation = $this->menu_variation_model->getFromId($variation_id);

        $results['isLoad'] = true;
        $results['variation'] = $variation;

        echo(json_encode($results));
    }

    public function saveMenuVariation(){

        $variation_id = $this->input->post('variation_id');
        $menu_id = $this->input->post('menu_id');
        $variation_title = $this->input->post('title');
        $variation_price = $this->input->post('price');
        $variation_back_staff_type = $this->input->post('staff_type');
        $back_staffs = $this->input->post('staff');
        $variation_back_amount = $this->input->post('amount');

        $results = [];
        if (empty($menu_id)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }

        if (empty($variation_id)){
            $variation = array(
                'menu_id' => $menu_id,
                'variation_title' => $variation_title,
                'variation_price' => $variation_price,
                'variation_back_amount' => empty($variation_back_amount) ? null : $variation_back_amount,
                'visible'=>'1',
            );

            $variation_id = $this->menu_variation_model->insertRecord($variation);
        }else{
            $variation = $this->menu_variation_model->getFromId($variation_id);
            $variation['variation_title'] = $variation_title;
            $variation['variation_price'] = $variation_price;
            $variation['variation_back_amount'] = empty($variation_back_amount) ? null : $variation_back_amount;

            $this->menu_variation_model->updateRecord($variation, 'variation_id');
        }

        $oldBackStaffs = $this->menu_variation_back_model->getVariationBacks($variation_id);
        foreach ($oldBackStaffs as $item){
            $this->menu_variation_back_model->delete_force($item['id'], 'id');
        }
        if (!empty($back_staffs)){
            $backs = json_decode($back_staffs);
            foreach ($backs as $item){
                $back_data = array(
                    'variation_id' => $variation_id,
                    'type'=> $variation_back_staff_type,
                    'staff_id' => $item,
                );
                $this->menu_variation_back_model->insertRecord($back_data);
            }

        }

        $results['isSave'] = true;

        echo(json_encode($results));
    }

    public function deleteMenuVariation(){

        $variation_id = $this->input->post('variation_id');

        $results = [];
        if (empty($variation_id)){
            $results['isDelete'] = false;
            echo json_encode($results);
            return;
        }
        $this->menu_variation_model->delete_force($variation_id, 'variation_id');

        $BackStaffs = $this->menu_variation_back_model->getVariationBacks($variation_id);
        foreach ($BackStaffs as $item){
            $this->menu_variation_back_model->delete_force($item['id'], 'id');
        }
        $results['isDelete'] = true;

        echo(json_encode($results));
    }

    public function loadViewMenuList(){

        $organ_id = $this->input->post('organ_id');

        $menu_list = $this->menu_model->getListByCond(['organ_id'=>$organ_id, 'is_user_menu' =>1]);

        $results['isLoad'] = true;
        $results['menus'] = $menu_list;

        echo(json_encode($results));
    }


    public function saveAdminMenu(){

        $menu_id = $this->input->post('menu_id');
        $organ_id = $this->input->post('organ_id');

        $menu_title = $this->input->post('title');
        $menu_detail = $this->input->post('detail');
        $menu_price = $this->input->post('price');
        $menu_week = $this->input->post('week');
        $menu_start_time = $this->input->post('start_time');
        $menu_end_time = $this->input->post('end_time');
        $menu_comment = empty($this->input->post('comment')) ? null : $this->input->post('comment');
        $menu_image = empty($this->input->post('image')) ? null : $this->input->post('image');

        if (empty($organ_id)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }

        if (empty($menu_id)){
            $menu = array(
                'organ_id' => $organ_id,
                'menu_title' => $menu_title,
                'menu_detail' => $menu_detail,
                'menu_price' => $menu_price,
                'menu_week' => $menu_week,
                'menu_start_time' => $menu_start_time,
                'menu_end_time' => $menu_end_time,
                'menu_comment' => $menu_comment,
                'menu_image' => $menu_image,
                'menu_type' => 2,
                'sort_no' => $this->menu_model->getMaxOrder($organ_id),
                'visible'=>'1',
            );

            $menu_id = $this->menu_model->InsertRecord($menu);

        }else{
            $menu = $this->menu_model->getFromId($menu_id);
            $menu['menu_title'] = $menu_title;
            $menu['menu_detail'] = $menu_detail;
            $menu['menu_price'] = $menu_price;
            $menu['menu_week'] = $menu_week;
            $menu['menu_start_time'] = $menu_start_time;
            $menu['menu_end_time'] = $menu_end_time;
            $menu['menu_comment'] = $menu_comment;
            if (!empty($menu_image)){
                $menu['menu_image'] = $menu_image;
            }

            $this->menu_model->updateRecord($menu, 'menu_id');

        }

        $results['isSave'] = true;

        $results['menu_id'] = $menu_id;
        echo(json_encode($results));
    }

    function uploadPicture() {

        $results = array();

        // user photo
        $image_path = "assets/images/menus/";
        if(!is_dir($image_path)) {
            mkdir($image_path);
        }
        $image_url  = base_url().$image_path;
        $fileName = $_FILES['picture']['name'];
        $config = array(
            'upload_path'   => $image_path,
            'allowed_types' => '*',
            'overwrite'     => 1,
            'file_name' 	=> $fileName
        );
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        $results['isUpload'] = false;
        if (!empty($_FILES['picture']['name'])) {
            if ($this->upload->do_upload('picture')) {
                $file_url = $image_url.$this->upload->file_name;
                $results['isUpload'] = true;
                $results['picture'] = $file_url;
            }
        }

        echo json_encode($results);

    }

    public function loadAdminMenuList(){
        $staff_id = $this->input->post('staff_id');

        $staff = $this->staff_model->getFromId($staff_id);

        $cond = [];
        if ($staff['staff_auth']<4){
            $organs = $this->staff_organ_model->getOrgansByStaff($staff_id);
            $cond['organ_ids'] = join(',' , array_column($organs,'organ_id'));
        }
        if ($staff['staff_auth']==4){
            $cond['company_id'] = $staff['company_id'];
        }

        $menus = $this->menu_model->getAdminMenuList($cond);

        $results['isLoad'] = true;
        $results['menus'] = $menus;

        echo json_encode($results);
    }

    public function deleteAdminMenu(){
        $menu_id = $this->input->post('menu_id');

        if (empty($menu_id)){
            $results['isDelete'] = false;
            echo json_encode($results);
            return;
        }
        $this->menu_model->delete_force($menu_id, 'menu_id');
        $results['isDelete'] = true;
        echo json_encode($results);
    }

    public function loadAdminMenuInfo(){
        $menu_id = $this->input->post('menu_id');
        $menu = $this->menu_model->getFromId($menu_id);

        $results['isLoad'] = true;
        $results['menu'] = $menu;

        echo json_encode($results);
    }

    public function exchangeMenuSort(){
        $move_menu_id = $this->input->post('move_menu');
        $target_menu_id = $this->input->post('target_menu');

        $move_menu = $this->menu_model->getFromId($move_menu_id);
        $target_menu = $this->menu_model->getFromId($target_menu_id);

        $move_menu_sort = $move_menu['sort_no'];
        $move_menu['sort_no'] = $target_menu['sort_no'];
        $target_menu['sort_no'] = $move_menu_sort;

        $this->menu_model->updateRecord($move_menu, 'menu_id');
        $this->menu_model->updateRecord($target_menu, 'menu_id');

        $results['isUpdate'] = true;

        echo json_encode($results);
    }

    public function loadMenuInfo(){
        $menu_id = $this->input->post('menu_id');

        $results = [];
        if (empty($menu_id) ){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }
        $menu = $this->menu_model->getFromId($menu_id);

        $results['isLoad'] = true;
        $results['menu'] = $menu;

        echo(json_encode($results));
    }

    public function loadVaritions(){
        $menu_id = $this->input->post('menu_id');

        $variations = $this->menu_variation_model->getVariationList(['menu_id'=>$menu_id]);

        $variation_data = [];
        foreach ($variations as $item){
            $back_list = $this->menu_variation_back_model->getVariationBacks($item['variation_id']);
            $item['backs'] = $back_list;
            $variation_data[] = $item;
        }


        $results['isLoad'] = true;

        $results['variations'] = $variation_data;

        echo json_encode($results);

    }
    public function loadBackStaffs(){
        $organ_id = $this->input->post('organ_id');
        $staffs = $this->staff_organ_model->getStaffsByOrgan($organ_id, 2);

        $results['staffs'] = $staffs;

        echo json_encode($results);
    }

    public function loadCompanyUserMenus(){
        $company_id = $this->input->post('company_id');

        $menus = $this->menu_model->getCompanyUserMenuList($company_id);

        $results['isLoad'] = true;
        $results['menus'] = $menus;

        echo json_encode($results);

    }

    public function loadMenuOrgans(){

        $condition = $this->input->post('condition');
        $cond = json_decode($condition, true);

        $organ_menus = $this->organ_menu_model->getListByCond($cond);

        $results['isLoad'] = true;
        $results['organ_menus'] = $organ_menus;

        echo(json_encode($results));
    }
}
?>
