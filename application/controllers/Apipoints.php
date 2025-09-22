<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

class Apipoints extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('organ_point_setting_model');
        $this->load->model('staff_point_add_model');
    }

    public function loadOrganPointSettings(){
        $organ_id = $this->input->post('organ_id');
        if (empty($organ_id)){
            $results['isLoad'] = false;
        }else{
            $settings = $this->organ_point_setting_model->getDataByParam(['organ_id'=>$organ_id]);
            $results['isLoad'] = true;
            $results['settings'] = $settings;
        }

        echo json_encode($results);
    }

   public function saveOrganPointSetting(){
        $organ_id = $this->input->post('organ_id');
        $title = $this->input->post('title');
       $point_value = $this->input->post('point_value');
       $point_type = $this->input->post('point_type');

        $data = array(
            'organ_id'=>$organ_id,
            'point_title' => $title,
            'point_value' => $point_value,
            'point_type' => $point_type,
        );

        $this->organ_point_setting_model->insertRecord($data);

        $results['isSave'] = true;
        echo json_encode($results);
    }
    public function deleteOrganPointSetting(){
        $organ_point_id = $this->input->post('organ_point_id');

        if (!empty($organ_point_id)){
            $this->organ_point_setting_model->delete_force($organ_point_id, 'organ_point_id');
        }

        $results['isDelete'] = true;
        echo json_encode($results);
    }

    public function submitPoint(){
        $staff_id = $this->input->post('staff_id');
        $organ_id = $this->input->post('organ_id');
        $point_date = $this->input->post('point_date');
        $point_setting_id = $this->input->post('point_setting_id');
        $point = $this->input->post('point');

        $point_setting = $this->organ_point_setting_model->getFromId($point_setting_id);
        $comment = empty($point_setting['point_title']) ? '設定なし' : $point_setting['point_title'];
        $weight = empty($point_setting['point_value']) ? '0' : $point_setting['point_value'];

        $data = array(
            'staff_id' => $staff_id,
            'point_date' => $point_date,
            'point_setting_id' => $point_setting_id,
            'organ_id' => $organ_id,
            'comment' => $comment,
            'point_weight' => $weight,
            'type' => empty($point_setting['point_type']) ? '1' : $point_setting['point_type'],
            'value' => $point,
            'status' => 1
        );

        $this->staff_point_add_model->insertRecord($data);

        $results['isSubmit'] = true;
        echo json_encode($results);
    }

    public function loadStaffPoints(){
        $condition = $this->input->post('condition');
        $cond = json_decode($condition, true);

        $staff_points = $this->staff_point_add_model->getStaffAddPoints($cond);

        $results['isLoad'] = true;
        $results['points'] = $staff_points;
        echo json_encode($results);
    }

    public function updatePointStatus(){
        $point_id = $this->input->post('point_id');
        $status = $this->input->post('status');

        $point = $this->staff_point_add_model->getFromId($point_id);
        $point['status'] = $status;

        $this->staff_point_add_model->updateRecord($point, 'id');

        $results['isUpdate'] = true;
        echo json_encode($results);
    }
    public function deleteStaffPoints(){
        $point_id = $this->input->post('point_id');

        $staff_points = $this->staff_point_add_model->delete_force($point_id, 'id');

        $results['isDelete'] = true;
        echo json_encode($results);
    }
}
?>
