<?php 
// if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

class EParkSync extends WebController
{
    
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('epark');

        $this->load->model('organ_model');
    }

    public function organsSync(){
        // $epark_helper = new Epark_helper(2);
        
        // $epark_organs = $epark_helper->searchOrgans('');

        // foreach($epark_organs as $item){
        //     $exist_organ = $this->organ_model->getOneByParam(['epark_id' => $item['id']]);
        //     if (empty($exist_organ)){
        //         $company_id = 2;
        //         $sync_organ = array(
        //             'company_id' => $company_id,
        //             'organ_number' => $this->organ_model->getMaxOrganNumber($company_id),
        //             'organ_name' => $item['name'],
        //             'phone' => $item['tel'],
        //             'epark_id' => $item['id'],
        //         );
        //         $this->organ_model->insertRecord($sync_organ);

        //     }else{
        //         $exist_organ['organ_name'] = $item['name'];
        //         $exist_organ['phone'] = $item['tel'];
        //         $this->organ_model->updateRecord($exist_organ, 'organ_id');
        //     }
        // }

        // var_dump('organ sync to pos');
    }
}

?>
