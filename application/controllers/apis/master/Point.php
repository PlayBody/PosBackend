<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

class Point extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('master/mst_point_rate_special_period_model');
        $this->load->model('master/mst_point_rate_special_limit_model');
    }

    /*
     * load organSpecialPeriodRate
     */
    public function getSpecialPeriodRatesByOrgan(){
        $organ_id = $this->input->post('organ_id');

        $rates = $this->mst_point_rate_special_period_model->getDataByParam(['organ_id' => $organ_id]);

        $results['is_load'] = false;
        if (!empty($rates)){
            $results = [
                'is_load' => true,
                'rates' => $rates
            ];
        }

        echo json_encode($results);
    }

    /*
     * save SpecialPeriodRatesByOrgan
     */
    public function saveSpecialPointRateSetting(){
        $id = $this->input->post('id');
        $organ_id = $this->input->post('organ_id');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $rate_days = $this->input->post('rate_days');
        $rate = $this->input->post('rate');

        if (empty($organ_id)) {
            echo json_encode(['is_save' => false]);
            return;
        }

        if (empty($id)){
            $this->mst_point_rate_special_period_model->insertRecord([
                'organ_id' => $organ_id,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'rate_days' => $rate_days,
                'rate' => $rate
            ]);
        }else{
            $data = $this->mst_point_rate_special_period_model->getFromId($id);
            $data['from_date'] = $from_date;
            $data['to_date'] = $to_date;
            $data['rate_days'] = $rate_days;
            $data['rate'] = $rate;

            $this->mst_point_rate_special_period_model->updateRecord($data, 'id');
        }

        echo json_encode(['is_save' => true]);
    }

    /*
     * delete SpecialPeriodRatesByOrgan
     */
    public function deleteSpecialPointRateSetting(){
        $id = $this->input->post('id');

        if (empty($id)) {
            echo json_encode(['is_delete' => false]);
            return;
        }

        $this->mst_point_rate_special_period_model->delete_force($id);

        echo json_encode(['is_delete' => true]);
    }



    /*
     * load organSpecialPeriodRate
     */
    public function getSpecialLimits(){
        $organ_id = $this->input->post('organ_id');

        $rates = $this->mst_point_rate_special_limit_model->getDataByParam(['organ_id' => $organ_id]);

        if (empty($rates)){
            echo json_encode(['is_load'=>false]);
            return;
        }

        echo json_encode(['is_load' => true,'rates' => $rates]);
    }


    /*
     * save SpecialPeriodRatesByOrgan
     */
    public function saveSpecialLimit(){
        $id = $this->input->post('id');
        $organ_id = $this->input->post('organ_id');
        $type = $this->input->post('type');
        $value = $this->input->post('value');
        $rate = $this->input->post('rate');

        if (empty($organ_id)) {
            echo json_encode(['is_save' => false]);
            return;
        }

        if (empty($id)){
            $this->mst_point_rate_special_limit_model->insertRecord([
                'organ_id' => $organ_id,
                'type' => $type,
                'value' => $value,
                'rate' => $rate
            ]);
        }else{
            $data = $this->mst_point_rate_special_limit_model->getFromId($id);
            $data['type'] = $type;
            $data['value'] = $value;
            $data['rate'] = $rate;

            $this->mst_point_rate_special_limit_model->updateRecord($data, 'id');
        }

        echo json_encode(['is_save' => true]);
    }

    /*
     * delete SpecialPeriodRatesByOrgan
     */
    public function deleteSpecialLimit(){
        $id = $this->input->post('id');

        if (empty($id)) {
            echo json_encode(['is_delete' => false]);
            return;
        }

        $this->mst_point_rate_special_limit_model->delete_force($id);

        echo json_encode(['is_delete' => true]);
    }



}
?>
