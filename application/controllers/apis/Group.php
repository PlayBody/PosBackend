<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Group extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('group_model');
    }

    public function loadGroupList(){
        $company_id = $this->input->post('company_id');
        if (empty($company_id)){
            $results['is_result'] = false;
            $results['err_message'] = "company_id is required";
            echo json_encode($results);
            return;
        }

        $groups = $this->group_model->getListByCond(['company_id'=>$company_id]);

        $results['is_result'] = true;
        $results['data'] = $groups;

        echo json_encode($results);
    }

}
?>
