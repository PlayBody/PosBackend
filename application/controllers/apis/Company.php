<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Company extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('company_model');
        $this->load->model('organ_model');
    }

    public function isSyncEpark(){
        $organ_id = $this->input->post('organ_id');
        $company_id = $this->input->post('company_id');

        if (!empty($organ_id)){
            $organ = $this->organ_model->getFromId($organ_id);
        }
    
        if (empty($company_id)){
            if (!empty($organ)){
                $company_id = $organ['company_id'];
            }
        }

        if (empty($company_id)){
            echo json_encode(['is_load' => false]);
            return;
        }

        $company = $this->company_model->getFromId($company_id);

        $is_sync = (!empty($company['is_sync_epark']) && $company['is_sync_epark'] ==1) ? true : false;

        if ($is_sync && !empty($organ)){
            if (empty($organ['epark_id'])) $is_sync = false;
        }

        echo json_encode(['is_load' => true, 'is_sync' => $is_sync]);
        return;
    }

}
?>
