<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Eparksync extends WebController
{
    /**
     * This is default constructor of the class
     */

    private $apiBase;
    private $authId;
    private $authPass;
    private $eparkCompanyId;
    private $apiKey;
    public function __construct()
    {
        parent::__construct();

        $this->load->model('epark_sync_config_model');
        $this->load->model('epark_sync_table_config_model');
    }

    public function index(){

//        $config = $this->epark_sync_config_model->getOneByParam(['company_id'=>2]);
//        $this->apiBase = $config['base_url'];
//        $this->authId = $config['auth_id'];
//        $this->authPass = $config['auth_pass'];
//        $this->eparkCompanyId = $config['epark_company_id'];
//
//        $this->authEpark();

        $tables = $this->epark_sync_table_config_model->getDataByParam(['company_id'=>2, 'is_real_update' => 1]);

        foreach ($tables as $table){
            $alias = $table['alias'];
            if ($alias=='Organ'){
                $this->organSync($table['company_id'], $table['from_type'], $table['from_date']);
            }

        }

        var_dump($tables);die();
    }

    private function authEpark(){
        $params = [
            'auth_id' => $this->authId,
            'auth_pass' => $this->authPass
        ];
        $result = $this->curl_post($this->apiBase.EPARK_FUNCTIONS['auth'], $params);
        if (empty($result['access_token_app'])){
            $this->epark_apikey = '';
        }
        $this->apiKey = $result['access_token_app'];
    }

    private function organSync($company_id, $from_type, $from_date){
        if (empty($from_type)) $from_type = 0;

        $params = [

        ];
        $result = $this->curl_post($this->apiBase.EPARK_FUNCTIONS['organ'], $params);

        $epark_organs = [];

//        foreach ()

    }

    private function curl_post($url, $params){
        $headers = [
            'Content-Type: application/json'
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return array();
        } else {
            $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($resultStatus != 200) {
                return array();
            }
            $result_array = (array)json_decode($result);
            if(empty($result_array["status"]) || empty($result_array["result"])){
                return array();
            }
            return (array)$result_array["result"];
        }
    }
}
?>