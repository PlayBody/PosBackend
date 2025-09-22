<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UpdateController.php';

class ShiftStatus extends UpdateController
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

        $this->header['page'] = 'admin';
        $this->header['sub_page'] = 'shift_status';
        $this->header['title'] = 'シフト状態管理';
        
        $this->load->model('shift_status_model');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $status_id = $this->input->get('id');
        $status = [];
        if (!empty($status_id)){
            $status = $this->shift_status_model->getFromId($status_id);
        }

        $statuses = $this->shift_status_model->getDataByParam([]);

        $this->data['status'] = $status;
        $this->data['statuses'] = $statuses;
        $this->load_view_with_menu("admin/shift_status");
    }

    public function save()
    {
        
        $id = $this->input->post('id');
		$title  = $this->input->post('title');
		$color  = $this->input->post('color');

        $status = [];
        if (!empty($id)) $status = $this->shift_status_model->getFromId($id);
        $status['title'] = $title;
        $status['color'] = $color;

        $this->load->library('form_validation');
        //$this->form_validation->set_rules('menu_id', 'menu_id', 'required', array('required'=>'メニューを選択してください。')); 
        $this->form_validation->set_rules('title', 'title', 'required', array('required'=>'タイトルを入力してください。')); 
        if ($this->form_validation->run() !== true){

            $statuses = $this->shift_status_model->getDataByParam([]);
            
            $this->data['status'] = $status;
            $this->data['statuses'] = $statuses;
            
            $this->load_view_with_menu("admin/shift_status");
            return;
        }

        
        if (empty($id)){
            $id = $this->shift_status_model->insertRecord($status);
        }else{
            $this->shift_status_model->updateRecord($status, 'id');
        }

        $this->session->set_flashdata('success', '保存しました。');
        
        redirect('/admin/shift_status?id='.$id);
    }

    public function delete(){
        $status_id = $this->input->post('id');

        if (empty($status_id)){            
            $this->session->set_flashdata('error', '操作が失敗しました。');
        }else{
            $this->shift_status_model->delete_force($status_id, 'id');
            $this->session->set_flashdata('success', 'データを削除しました。');
        }
        
        redirect('/admin/shift_status');
    }

}
