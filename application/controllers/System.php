<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';

class System extends AdminController
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

        $this->header['page'] = 'system';
        $this->header['sub_page'] = 'shift';
        $this->header['title'] = 'シフトデータエラー';

    }

    /**
     * This function used to load the first screen of the user
     */
    public function shift()
    {
        $this->load->model('shift_model');

        $errors = $this->shift_model->getErrorShift();
        
        $list = [];
        foreach($errors as $error){
            $tmp = [];
            $shift_ids = explode(',', $error['shift_ids']);
            foreach ($shift_ids as $shift_id){
                $shift = $this->shift_model->getShiftDataFromId($shift_id);
                $tmp[] = $shift;
            }
            $list[] = $tmp;
        }



        $this->data['list'] = $list;
        $this->load_view_with_menu("system/shift");
    }

    public function shiftDelete(){
        $shift_id = $this->input->get('shift_id');
        if (empty($shift_id)){
            redirect('system/shift');
        }
        $this->load->model('shift_model');
        $this->shift_model->delete_force($shift_id, 'shift_id');
        redirect('system/shift');
    }

}
