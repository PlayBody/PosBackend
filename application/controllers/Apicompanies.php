<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

class Apicompanies extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('company_model');
        $this->load->model('organ_model');
        $this->load->model('company_site_model');
    }

    public function loadCompanyInfo(){
        $company_id = $this->input->post('company_id');

        if (empty($company_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $company = $this->company_model->getFromId($company_id);

        if (empty($company)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $results['isLoad'] = true;
        $results['company'] = $company;

        echo(json_encode($results));
    }

    public function loadPrintCompanyInfo(){
        $company_id = $this->input->post('company_id');

        if (empty($company_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $company = $this->company_model->getFromId($company_id);

        if (empty($company)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }


        $company['print_order_number'] = empty($company['print_order_number']) ? 1 : ($company['print_order_number']+1);

        $this->company_model->updateRecord($company, 'company_id');

        $results['isLoad'] = true;
        $results['company'] = $company;
        
        echo(json_encode($results));
    }

    public function loadCompanyList(){
        $companies = $this->company_model->getListByCond([]);

        $results['isLoad'] = true;
        $results['companies'] = $companies;
        echo(json_encode($results));

    }

    public function loadCompanyData(){
        $company_id = $this->input->post('company_id');

        if (empty($company_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $company = $this->company_model->getFromId($company_id);

        $organs = $this->organ_model->getListByCond(['company_id'=>$company_id]);

        $results['isLoad'] = true;
        $results['company'] = $company;
        $results['organs'] = $organs;
        echo(json_encode($results));

    }

    public function saveCompany(){
        $company_id = $this->input->post('company_id');
        $company_name = $this->input->post('company_name');
        $company_domain = $this->input->post('company_domain');
        $ec_site_url = $this->input->post('ec_site_url');
        $company_receipt_number = $this->input->post('company_receipt_number');


        if (empty($company_id)){
            $company['company_name'] = $company_name;
            $company['company_domain'] = $company_domain;
            $company['ec_site_url'] = $ec_site_url;
            $company['company_receipt_number'] = $company_receipt_number;
            $company['visible'] = 1;

            $company_id = $this->company_model->insertRecord($company);
        }else{
            $company = $this->company_model->getFromId($company_id);
            $company['company_name'] = $company_name;
            $company['company_domain'] = $company_domain;
            $company['ec_site_url'] = $ec_site_url;
            $company['company_receipt_number'] = $company_receipt_number;

            $this->company_model->edit($company, 'company_id');
        }

        $results['isSave'] = true;
        $results['company_id'] = $company_id;

        echo(json_encode($results));

    }

    public function saveOrgan(){
        $company_id = $this->input->post('company_id');
        $organ_id = $this->input->post('organ_id');
        $organ_name = $this->input->post('organ_name');

        if (empty($organ_id)){
            $organ['company_id'] = $company_id;
            $organ['organ_name'] = $organ_name;
            $organ['visible'] = 1;
            $organ['organ_number'] = $this->organ_model->getMaxOrganNumber($company_id);

            $organ_id = $this->organ_model->insertRecord($organ);
        }else{
            $organ = $this->organ_model->getFromId($organ_id);
            $organ['organ_name'] = $organ_name;
            if (empty($organ['organ_number'])) $organ['organ_number'] = $this->organ_model->getMaxOrganNumber($company_id);

            $this->organ_model->updateRecord($organ, 'organ_id');
        }

        $results['isSave'] = true;

        echo(json_encode($results));

    }

    public function deleteCompany(){
        $company_id = $this->input->post('company_id');
        $is_restore = $this->input->post('is_restore');

        if (empty($company_id)){
            $results['isDelete'] = false;
            echo json_encode($results);
            return;
        }

        $company = $this->company_model->getFromId($company_id);
        $company['visible'] = empty($is_restore) ? 0 : 1;
        $this->company_model->updateRecord($company, 'company_id');

        $results['isDelete'] = true;

        echo(json_encode($results));

    }

    public function deleteOrgan(){
        $organ_id = $this->input->post('organ_id');

        if (empty($organ_id)){
            $results['isDelete'] = false;
            echo json_encode($results);
            return;
        }

//        $this->company_model->delete($organ_id, 'organ_id');

        $this->organ_model->delete_force($organ_id, 'organ_id');

        $results['isDelete'] = true;

        echo(json_encode($results));

    }

    public function getCompanySites(){
        $company_id = $this->input->post('company_id');

        if (empty($company_id)){
            $results['sites'] = [];
            echo json_encode($results);
            return;
        }

        $sites = $this->company_site_model->getListByCond(['company_id'=>$company_id]);

        $results['sites'] = $sites;
        echo json_encode($results);
    }

    public function saveCompanySite(){
        $company_id = $this->input->post('company_id');
        $site_id = $this->input->post('site_id');
        $site_title = $this->input->post('site_title');
        $site_url = $this->input->post('site_url');

        if (empty($site_id)){
            $site = array(
                'company_id'=>$company_id,
                'site_title' => $site_title,
                'site_url' => $site_url,
                'visible' => 1
            );
            $this->company_site_model->insertRecord($site);
        }else{
            $site = $this->company_site_model->getFromId($site_id);

            $site['site_title'] = $site_title;
            $site['site_url'] = $site_url;

            $this->company_site_model->updateRecord($site, 'id');
        }

        $results['isSave'] = true;
        echo json_encode($results);
    }

    public function deleteCompanySite(){
        $site_id = $this->input->post('site_id');

        if (!empty($site_id)){
            $this->company_site_model->delete_force($site_id, 'id');
        }

        $results['isDelete'] = true;
        echo json_encode($results);
    }

    public function loadStampCountPrevRank(){
        $company_id = $this->input->post('company_id');
        $cur_rank = $this->input->post('rank');
        $this->load->model('rank_model');
        $cond['company_id'] = $company_id;
        $cond['prev_rank'] = $cur_rank;
        $ranks_data = $this->rank_model->getRankList($cond);
        $stamps = 0;
        foreach($ranks_data as $rank){
            $stamps += $rank['max_stamp'];
        }

        $results['isLoad'] = true;
        $results['stamp_count'] = $stamps;
        echo json_encode($results);
    }
}
?>