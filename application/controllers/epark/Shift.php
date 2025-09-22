<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';

class Shift extends AdminController
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

        $this->header['page'] = 'epark';
        $this->header['sub_page'] = 'shift';
        $this->header['title'] = '月間シフト作成'; //Monthly shift creation.


        $this->load->model('company_model');
        $this->load->model('organ_model');
        $this->load->model('shift_model');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $company_id = 2;
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $organ_id = $this->input->post('organ_id');

        $organ_list = $this->organ_model->getDataByParam(['company_id' => $company_id]);
        if (empty($organ_id)) $organ_id = $organ_list[0]['organ_id'];
        $this->data['organs'] = $organ_list;
        $this->data['organ_id'] = $organ_id;

        $staff_list = $this->staff_model->getStaffs(['organ_id' => $organ_id]);
        $this->data['staffs'] = $staff_list;

        if (empty($year)) $year = date('Y');
        if (empty($month)) $month = '06';date('m');

        $sel_date = $year . '-' . $month;

        $cur_date = $sel_date . '-' . '01';
        $_time = new DateTime($cur_date);
        $_time->add(new DateInterval('P1M'));
        $next_first_date = $_time->format('Y-m-d');

        $cur_time = new DateTime($cur_date);
        $shift_data = [];

        // To Fix php version problem....
        $weeks = array('日', '月', '火', '水', '木', '金', '土');

        while($cur_date<$next_first_date){
            $tmp = [];
            $day = $cur_time->format('d');
            $weekday = $cur_time->format('w');
            $tmp['day_string'] = $cur_time->format('m月d日').'('.$weeks[$weekday].')';
            $shifts = $this->shift_model->getListByCond(['select_date' => $cur_date]);
            foreach ($shifts as $shift){
                $tmp[$shift['staff_id']][] = $shift;
            }
            $shift_data[] = $tmp;
            $cur_time->add(new DateInterval('P1D'));
            $cur_date = $cur_time->format('Y-m-d');
        }

        $this->data['shifts'] = $shift_data;
        $this->data['string_date'] = $year . '年' . $month . '月';

        $this->load_view_with_menu("epark/shift_index.php");
    }
}
?>
