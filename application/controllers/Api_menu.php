<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

class Api_menu extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('pos_menu_model');
        $this->load->model('pos_staff_model');
        $this->load->model('pos_member_model');
        $this->load->model('pos_setting_model');
    }

    public function load_menu(){

    	$staff_id = $this->input->post('staff_id');

    	$results = [];
    	if (empty($staff_id)){
    		$results['isLoaded'] = false;
    		echo json_encode($results);
    		return;
    	}

        $staff = $this->pos_staff_model->getSettings($staff_id);

    	if (empty($staff) || empty($staff['member_id'])){
    		$results['isLoaded'] = false;
    		echo json_encode($results);
    		return;
    	}

        $count = empty($staff['menu_count']) ? 4 : $staff['menu_count'];

        if (empty($count)){
            $count = 4;
        }

        $menus = $this->pos_menu_model->getMenus($staff['member_id'], $count);

        $results['isLoaded'] = true;
        $results['menu'] = $menus;

        echo(json_encode($results));
    }

    public function load_menu_list(){

        $this->load->model('pos_accounting_model');
        $this->load->model('pos_accounting_menu_model');

        $staff_id = $this->input->post('staff_id');
        $position = $this->input->post('position');

        $results = [];
        if (empty($staff_id) || empty($position)){

            $results['isLoad'] = false;
            echo(json_encode($results));
            exit(0);
        }

        $staff = $this->pos_staff_model->getFromId($staff_id);

        $account = $this->pos_accounting_model->getRecord($staff['member_id'], $position);

        if (empty($account)){

            $results['isLoad'] = false;
            echo(json_encode($results));
            exit(0);
        }
        

        $accounting_id = $account['id'];

        $list = $this->pos_accounting_menu_model->getOrderList($accounting_id);

        if (empty($list)){
            $results['isLoad'] = false;
        }else{
            $results['isLoad'] = true;
            $results['list'] = $list;            
        }

        echo(json_encode($results));
    }

    public function reg_accounting_menu(){

        $this->load->model('pos_accounting_model');
        $this->load->model('pos_accounting_menu_model');


        $staff_id = $this->input->post('staff_id');
        $position = $this->input->post('position');
        $data = $this->input->post('data');

        if (empty($staff_id) || empty($position)){
            echo(json_encode(array('isSave'=>false)));
            exit(0);
        }

        $staff = $this->pos_staff_model->getFromId($staff_id);
        if (empty($staff['member_id'])){
            return;
        }

        $account = $this->pos_accounting_model->getRecord($staff['member_id'], $position);

        if (empty($account)){
            echo(json_encode(array('isSave'=>false)));
            exit(0);
        }

        $accounting_id = $account['id'];

        $this->pos_accounting_menu_model->delete_force($accounting_id, 'accounting_id');
      
        $data = json_decode($data);
  
        foreach ($data as $record) {
            $insertData = [];
            $insertData = array(
                'title' => $record->title,
                'amount' => $record->amount,
                'quantity' => $record->quantity,
                'accounting_id' => $accounting_id,
                'del_flag' => 0,
                'create_date' => date('Y-m-d'),
                'update_date' => date('Y-m-d'),
            );
            if (!empty($record->menu_id)){
                $insertData['menu_id'] = $record->menu_id;
            }

            $insert = $this->pos_accounting_menu_model->add($insertData);

        }
        echo(json_encode(array('isSave'=>true)));
        exit(0);
    } 

    public function load_menu_all(){

        $staff_id = $this->input->post('staff_id');

        $results = [];
        if (empty($staff_id)){
            $results['isLoaded'] = false;
            echo json_encode($results);
            return;
        }

        $staff = $this->pos_staff_model->getSettings($staff_id);

        if (empty($staff) || empty($staff['member_id'])){
            $results['isLoaded'] = false;
            echo json_encode($results);
            return;
        }

        $menus = $this->pos_menu_model->getMenus($staff['member_id'], '');

        $results['isLoaded'] = true;
        $results['menus'] = $menus;

        echo(json_encode($results));
    }

    public function load_menu_detail(){

        $menu_id = $this->input->post('menu_id');

        $results = [];
        if (empty($menu_id)){
            $results['isLoaded'] = false;
            echo json_encode($results);
            return;
        }

        $menu = $this->pos_menu_model->getFromId($menu_id);

        $results['isLoaded'] = true;
        $results['menu'] = $menu;

        echo(json_encode($results));
    }

    public function add_menu_info(){

        $staff_id = $this->input->post('staff_id');

        $results = [];
        if (empty($staff_id)){
            $results['isAdded'] = false;
            echo json_encode($results);
            return;
        }

        $staff = $this->pos_staff_model->getFromId($staff_id);

        if (empty($staff) || empty($staff['member_id'])){
            $results['isAdded'] = false;
            echo json_encode($results);
            return;
        }

        $maxSort = $this->pos_menu_model->getMaxOrder($staff['member_id']);

        $menu = [];
        $menu['member_id'] = $staff['member_id'];
        $menu['title'] = $this->input->post('title');
        $menu['amount'] = $this->input->post('amount');
        $menu['sort_no'] = $maxSort;
        $menu['del_flag'] = '0';
        $menu['create_date'] = date('Y-m-d H:i:s');
        $menu['update_date'] = date('Y-m-d H:i:s');

        $menus = $this->pos_menu_model->add($menu);

        $results['isAdded'] = true;

        echo(json_encode($results));
    }

    public function update_menu_info(){

        $menu_id = $this->input->post('menu_id');

        $results = [];
        if (empty($menu_id)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $menu = $this->pos_menu_model->getFromId($menu_id);

        if (empty($menu)){
            $results['isUpdate'] = false;
            echo json_encode($results);
            return;
        }

        $menu['title'] = $this->input->post('title');
        $menu['amount'] = $this->input->post('amount');
        $menu['update_date'] = date('Y-m-d H:i:s');

        $menus = $this->pos_menu_model->edit($menu, 'id');

        $results['isUpdate'] = true;

        echo(json_encode($results));
    }

    public function delete_selected_menu(){

        $menu_ids = $this->input->post('checkIds');

        $ids = json_decode($menu_ids);

        $delIds = [];
        foreach ($ids as $value) {
            $delIds[] = (int)$value;
        }

        $results = [];

        $results['isDelete'] = false;

        if (!empty($delIds)) {
            $this->pos_menu_model->deleteMultiRow($delIds) ;

            $results['isDelete'] = true;
        }       

        echo json_encode($results);

        return;
    }

    function load_crm_menu(){
        $member_id = $this->input->post('member_id');

        $results = [];
        if (empty($member_id)){
            $results['isLoaded'] =false;
            echo json_encode($results);
            return;
        }

        $member = $this->pos_member_model->getFromId($member_id);

        $count = empty($member['menu_count']) ? 4 : $member['menu_count'];

        $menus = $this->pos_menu_model->getMenus($member_id, $count);

        $results['isLoaded'] = true;
        $results['menus'] = $menus;

        echo(json_encode($results));

    }

}
?>