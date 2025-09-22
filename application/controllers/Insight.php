<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/AdminController.php';
/**
 * Class : Insight
 *
 */
class Insight extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_COMPANY);

        $this->load->model('faq_model');
        $this->load->model('setting_model');
        $this->load->model('analytics_model');

        //
        $this->data['page'] = 'insight';
        $this->header['title'] = '';
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->data['company'] = $this->company_model->get($this->user['company_id']);
        $wix_domain = $this->data['company']['company_wix_domain'];
        $wix_api_key = $this->data['company']['company_wix_key'];
        $wix_api_secret = $this->data['company']['company_wix_secret'];


        $this->data['searchText'] = $this->input->post('searchText');
        $search  = array(
            'searchText'=> $this->data['searchText'],
        );
        $this->data['list_cnt']  = $this->analytics_model->getList("*",$search ,true);
        $this->load->library('pagination');
        $returns = $this->_paginationCompress ( "insight/index", $this->data['list_cnt'], 10,3 );

        $this->data['list']  = $this->analytics_model->getList("*",$search ,false,$returns["page"],$returns["segment"]);

        $this->_load_view("insight/index");
    }

    public function download(){

        ini_set('max_execution_time', 0);
        $page_count = 100;

        $filename = "FAQリスト_".date('YmdHis').".csv";
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$filename);

        $fp = fopen('php://output', 'w');
        $csv_header = array(
            'id',
            'title',//タイトル
            'content',//内容
        );
        fputcsv($fp, $csv_header);

        $cnt = 0;
        do{
            $result = $this->faq_model->getList("*",'',false,$page_count,$cnt);
            foreach ($result as $item){
                $items = (array)$item;
                $title = mb_convert_encoding($items['title'],'SJIS-win', 'UTF-8');
                $content = mb_convert_encoding($items['content'],'SJIS-win', 'UTF-8');
                $csv_line = array(
                    'id'=>$items['id'],
                    'title'=>$title,
                    'content'=>$content,
                );
                fputcsv($fp, $csv_line);
                $cnt++;
            }
        }while(!empty($result));

        fclose($fp);
        exit;
//        $this->debug($list);
    }

    public function view_sync(){

        $start_time = microtime(true);
        ini_set('max_execution_time', 0);
        $this->data['company'] = $this->company_model->get($this->user['company_id']);

        $wix_domain = $this->data['company']['company_wix_domain'];
        $wix_api_key = $this->data['company']['company_wix_key'];
        $wix_api_secret = $this->data['company']['company_wix_secret'];

        $start_date = $this->setting_model->getSetting('faq_search',$this->user['company_id']);
        $time = strtotime($start_date)+86400*30;
        if($time>time()){
            $time = time();
        }

        $end_date = date('Y-m-d',$time);
        $this->data['anal_data'] = $this->wix_analytics('https://'.$wix_domain.'.wixanswers.com/api/v1/',$wix_api_key,$wix_api_secret,$start_date,$end_date);

        $cnt=0;
        foreach ($this->data['anal_data'] as $item){
            if(isset($item->histogram)){

                foreach ($item->histogram as $histogram){
                    $faq = array(
                        'company_id'=>$this->user['company_id'],
                        'faq_id'=>$item->id,
                        'view_date'=>date('Y-m-d',$histogram->x/1000),
                        'view_cnt'=> $histogram->y,
                    );
                    $this->analytics_model->registerTwoKey($faq,'faq_id','company_id');
                    $cnt++;
                }
            }
        }
        $this->setting_model->setSetting('faq_search',date('Y-m-d'),$this->user['company_id']);
        $res = array('ok'=>true,'cnt'=>$cnt);
        return $this->output->set_output(json_encode( $res ));
    }
}

?>