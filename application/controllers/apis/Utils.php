<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Utils extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        //        $this->load->model('pos_staff_shift_model');
    }

    public function loadRanks()
    {
		$company_id = $this->input->post('company_id');
		if (empty($company_id)) {
			$result['is_load'] = false;
            echo json_encode($result);
            return;
		}

		$this->load->model('rank_model');

		$ranks = $this->rank_model->getDataByParam(['company_id' => $company_id]);

		$result['is_load'] = true;
		$result['data'] = $ranks;
		echo json_encode($result);

    }

    public function loadUserStampCount(){
        $user_id = $this->input->post('user_id');

        $cond = [];
        $cond['user_id'] = $user_id;
        $cond['use_flag'] = 0;

		$this->load->model('stamp_model');
        $stamps = $this->stamp_model->getStampList($cond);

        $results['is_load'] = true;
        $results['count'] = count($stamps);

        echo json_encode($results);
    }
}
?>
