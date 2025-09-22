<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/AdminController.php';

/**
 * Class : Faq
 *
 */
class Faq extends AdminController
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

        //FAQ一式をダウンロード
        $this->data['page'] = 'faq';
        $this->header['title'] = 'FAQ一覧';
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->data['company'] = $this->company_model->get($this->user['company_id']);

        $this->data['searchText'] = $this->input->post('searchText');
        $this->data['start_date'] = $this->input->post('start_date');
        $this->data['end_date'] = $this->input->post('end_date');
        if(empty($this->data['start_date'] )) $this->data['start_date'] =  date('Y-m-d',strtotime('-1 months'));
        if(empty($this->data['end_date'] )) $this->data['end_date'] =  date('Y-m-d');
        $search = array(
            'start_date' => $this->data['start_date'],
            'end_date' => $this->data['end_date'],
            'company_id' => $this->user['company_id']
        );
        $this->data['list_cnt'] = $this->faq_model->getList("*", $search, true);
        $this->load->library('pagination');
        $returns = $this->_paginationCompress("faq/index", $this->data['list_cnt'], 10, 3);

        $this->data['start_page'] = $returns["segment"] + 1;
        $this->data['end_page'] = $returns["segment"] + $returns["page"];
        if ($this->data['end_page'] > $this->data['list_cnt']) $this->data['end_page'] = $this->data['list_cnt'];
        if (!$this->data['start_page']) $this->data['start_page'] = 1;
        $this->data['list'] = $this->faq_model->getList("", $search, false, $returns["page"], $returns["segment"]);
        $this->data['faq_date'] = $this->setting_model->getSetting('faq_date', $this->user['company_id']);
        $this->data['anal_date'] = $this->setting_model->getSetting('anal_date', $this->user['company_id']);

        $this->_load_view("faq/index");
    }

    public function download()
    {

        ini_set('max_execution_time', 0);
        $page_count = 100;

        $filename = "FAQリスト_" . date('YmdHis') . ".csv";
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);

        $fp = fopen('php://output', 'w');
        $csv_header = array(
            'ID',
            'タイトル',//タイトル
            '内容',//内容
            '閲覧数',//閲覧数
            'カテゴリID',
            '公開状態',
            '作成者',
            '作成日',
            '最終更新日',
            '最初の公開日',
            '最終公開日',
        );
        foreach ($csv_header as &$item) {
            $item = mb_convert_encoding($item, 'SJIS-win', 'UTF-8');
        }
        fputcsv($fp, $csv_header);

        $this->data['searchText'] = $this->input->post('searchText');
        $this->data['start_date'] = $this->input->post('start_date');
        $this->data['end_date'] = $this->input->post('end_date');
        if(empty($this->data['start_date'] )) $this->data['start_date'] =  date('Y-m-d',strtotime('-1 months'));
        if(empty($this->data['end_date'] )) $this->data['end_date'] =  date('Y-m-d');
        $search = array(
            'start_date' => $this->data['start_date'],
            'end_date' => $this->data['end_date'],
            'company_id' => $this->user['company_id']
        );
        $cnt = 0;
        do {
            $result = $this->faq_model->getList("", $search, false, $page_count, $cnt);
            foreach ($result as $item) {
                $items = (array)$item;
                $title = mb_convert_encoding($items['title'], 'SJIS-win', 'UTF-8');
                $content = mb_convert_encoding($items['content'], 'SJIS-win', 'UTF-8');
                $csv_line = array(
                    'id' => $items['id'],
                    'title' => $title,
                    'content' => $content,
                    'view' => $items['view'],
                    'categoryId' => $items['categoryId'],
                    'status' => '',
                    'author' => $items['author'],
                    'creationDate' => '',
                    'lastUpdateDate' => '',
                    'firstPublishDate' => '',
                    'lastPublishDate' => '',
                );
                if (!empty($items['status']) && $items['status']) {
                    if ($items['status'] == 30) $csv_line['status'] = mb_convert_encoding('削除済み', 'SJIS-win', 'UTF-8');
                    if ($items['status'] == 10) $csv_line['status'] = mb_convert_encoding('公開済み', 'SJIS-win', 'UTF-8');
                    if ($items['status'] == 0) $csv_line['status'] = mb_convert_encoding('ドラフト', 'SJIS-win', 'UTF-8');
                }
                if (!empty($items['creationDate'])) {
                    $csv_line['creationDate'] = $items['creationDate'];
                }
                if (!empty($items['lastUpdateDate'])) {
                    $csv_line['lastUpdateDate'] = $items['lastUpdateDate'];
                }
                if (!empty($items['firstPublishDate'])) {
                    $csv_line['firstPublishDate'] = $items['firstPublishDate'];
                }
                if (!empty($items['lastPublishDate'])) {
                    $csv_line['lastPublishDate'] = $items['lastPublishDate'];
                }
                fputcsv($fp, $csv_line);
//                $cnt++;
            }
            $cnt+=$page_count;
        } while (!empty($result));

        fclose($fp);
        exit;
//        $this->debug($list);
    }

    public function get_user()
    {
        ini_set('max_execution_time', 0);
        $this->data['company'] = $this->company_model->get($this->user['company_id']);

        $wix_api_domain = $this->data['company']['company_wix_domain'];
        $wix_api_key = $this->data['company']['company_wix_key'];
        $wix_api_secret = $this->data['company']['company_wix_secret'];

        $guid = '70b48832-e802-4fdc-a006-6aafb8bb9337';
//        [id] => 70b48832-e802-4fdc-a006-6aafb8bb9337
//        [email] => info@proz.jp
//        [emailStatus] => 1
//    [creationDate] => 1614141787000
//    [lastUpdateDate] => 1625038762000
//    [permissionLevel] => 80
//    [isAgent] => 1
//    [userType] => 1
//    [firstName] => 佐藤
//        [signature] =>
//    [banned] =>
//    [fullName] => 佐藤
//        [uri] => /user/70b48832-e802-4fdc-a006-6aafb8bb9337
//        [hasName] => 1
//    [hasEmail] => 1
//    [imported] =>
//
        $this->wix_get_user($guid, $wix_api_domain, $wix_api_key, $wix_api_secret);
    }

    public function faq_sync()
    {

        $start_time = microtime(true);
        ini_set('max_execution_time', 0);
        $this->data['company'] = $this->company_model->get($this->user['company_id']);

        $wix_api_domain = $this->data['company']['company_wix_domain'];
        $wix_api_key = $this->data['company']['company_wix_key'];
        $wix_api_secret = $this->data['company']['company_wix_secret'];

        $list = array();
        $page = 1;
        $page_per = 100;

        $page_count = 0;
        do {
            $result = $this->wix_article_list("", $page, $page_per, $wix_api_domain, $wix_api_key, $wix_api_secret);
            if (empty($result)) break;

            $page_count = $result['itemsCount'];
            if ($result['itemsCount'] > 0) {
                foreach ($result['items'] as $item) {
                    $items = (array)$item;
                    $row = array(
                        'id' => $items['id'],
                        'company_id' => $this->user['company_id'],
                        'title' => $items['title'],
                        'content' => $items['content'],
                        'categoryId' => $items['categoryId'],
                        'status' => $items['status'],
                        'author' => $items['author']->id,
                        'url' => $items['url'],
                    );
                    if (!empty($items['draftTitle'])) {
                        $row['draftTitle'] = $items['draftTitle'];
                    }
                    if (!empty($items['draftContent'])) {
                        $row['draftContent'] = $items['draftContent'];
                    }
                    if (!empty($items['firstPublishDate'])) {
                        $row['firstPublishDate'] = date('Y-m-d H:i:s', $items['firstPublishDate'] / 1000);
                    }
                    if (!empty($items['lastPublishDate'])) {
                        $row['lastPublishDate'] = date('Y-m-d H:i:s', $items['lastPublishDate'] / 1000);
                    }
                    if (!empty($items['creationDate'])) {
                        $row['creationDate'] = date('Y-m-d H:i:s', $items['creationDate'] / 1000);
                    }
                    if (!empty($items['lastUpdateDate'])) {
                        $row['lastUpdateDate'] = date('Y-m-d H:i:s', $items['lastUpdateDate'] / 1000);
                    }
                    if (!empty($items['contentLastUpdateDate'])) {
                        $row['contentLastUpdateDate'] = date('Y-m-d H:i:s', $items['contentLastUpdateDate'] / 1000);
                    }

                    $this->faq_model->register($row);
                }
                $page = $result['nextPage'];
            } else {
                break;
            }

        } while ($result['itemsCount'] != $result['to']);

        $this->setting_model->setSetting('faq_date', date('Y-m-d'), $this->user['company_id']);
        $res = array(
            'ok' => true,
            'cnt' => $page_count,
            'time' => (microtime(true) - $start_time),
        );

        return $this->output->set_output(json_encode($res));
    }


    public function anal_sync()
    {
        $this->load->model('setting_model');
        $start_time = microtime(true);
        ini_set('max_execution_time', 0);
        $this->data['company'] = $this->company_model->get($this->user['company_id']);

        $wix_domain = $this->data['company']['company_wix_domain'];
        $wix_api_key = $this->data['company']['company_wix_key'];
        $wix_api_secret = $this->data['company']['company_wix_secret'];

        $cnt = 0;

        $start_date = '';//$this->setting_model->getSetting('anal_date', $this->user['company_id']);
        if (empty($start_date)) {
            $time = strtotime($this->data['company']['create_date']) - 86400 * 365;
            if ($time < time() - 86400 * 365) {
                $time = time() - 86400 * 365;
            }
            $start_date = date('Y-m-d', $time);
            //$start_date = date('Y-m-d', time() - 86400 * 30);
        }
        $time = strtotime($start_date) + 86400 * 30;
        if ($time > time()) {
            $time = time();
        }
        $end_date = date('Y-m-d', $time);
        do {
            $this->data['anal_data'] = $this->wix_analytics($wix_domain, $wix_api_key, $wix_api_secret, $start_date, $end_date);

            foreach ($this->data['anal_data'] as $item) {
                if (isset($item->histogram)) {

                    foreach ($item->histogram as $histogram) {
                        $faq = array(
                            'company_id' => $this->user['company_id'],
                            'faq_id' => $item->id,
                            'view_date' => date('Y-m-d', $histogram->x / 1000),
                            'view_cnt' => $histogram->y,
                        );
                        $cnt++;
                        $this->analytics_model->registerTwoKey($faq, 'faq_id', 'company_id');
                    }
                }
            }
            $start_date = $end_date;
            $time = strtotime($start_date) + 86400 * 30;
            if ($time > time()) {
                $time = time();
            }
            $end_date = date('Y-m-d', $time);
        } while ($start_date != $end_date || strtotime($start_date) >= time());

        //$this->setting_model->setSetting('anal_date', $end_date, $this->user['company_id']);

        $res = array(
            'ok' => true,
            'cnt' => $cnt,
            'time' => (microtime(true) - $start_time),
        );

        return $this->output->set_output(json_encode($res));
    }
}

?>