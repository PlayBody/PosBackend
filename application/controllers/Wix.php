<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';
/**
 * Class : Wix
 *
 */
class Wix extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_COMPANY);
        $this->load->model('scenario_model');
        $this->global['page'] = 'faq';
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {

        $this->global['pageTitle'] = 'FAQ一式をダウンロード';

        $data['list'] = $this->scenario_model->getScenarioList();
//        print_r($data['list'][0]);die;

        $this->global['pageTitle'] = 'FAQ一覧';

        $data['wix_url'] = WIX_API_URL;
        $data['wix_api_key'] = WIX_API_KEY;
        $data['wix_api_secret'] = WIX_API_SECRET;

        $mode = $this->input->post('mode');
        if($mode=='update'){
            $this->user_model->updateWixInfo();
        }
        $this->loadViews("faq/index", $this->global, $data , NULL);
    }

    public function download(){

        $wix_api_key = $this->input->post('wix_api_key');
        $wix_api_secret = $this->input->post('wix_api_secret');

        $list = array();
        $page = 1;
        $page_count = 10;

        $filename = "reports.csv";
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$filename);

        $fp = fopen('php://output', 'w');
        $csv_header = array(
            'id',
            'title',//タイトル
            'content',//内容
        );
        fputcsv($fp, $csv_header);

        do{
            $result = $this->wix_article_list("",$page,$page_count,$wix_api_key,$wix_api_secret);
            if($result['itemsCount']>0){

                foreach ($result['items'] as $item){
                    $items = (array)$item;
                    $title = mb_convert_encoding($items['title'],'SJIS-win', 'UTF-8');
                    $content = mb_convert_encoding($items['content'],'SJIS-win', 'UTF-8');
                    $csv_line = array(
                        'id'=>$items['id'],
                        'title'=>$title,
                        'content'=>$content,
                    );
                    fputcsv($fp, $csv_line);
                }
            }else{
                break;
            }
        }while($result['itemsCount']==$page_count);

        fclose($fp);
        exit;
//        $this->debug($list);
    }
}

?>