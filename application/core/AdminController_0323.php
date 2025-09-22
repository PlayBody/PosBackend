<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class AdminController
 */
class AdminController extends CI_Controller
{
    public $data;
    public $header;
    public $footer;
    public $staff;

    /**
     * Class constructor
     *
     * @return    void
     */
    public function __construct($role = ROLE_GUEST)
    {
        parent::__construct();

        $this->header['page'] = $this->uri->segment(1);
        $this->header['role'] = $role;
        if (!$this->_login_check($role)) {
            if ($role == ROLE_ADMIN) redirect('/admin/login');
            else if ($role == ROLE_COMPANY) redirect('/login');
        } else {
            if ($role == ROLE_ADMIN) $this->header['title'] = '管理画面【管理者用】';
            else if ($role == ROLE_COMPANY) $this->header['title'] = '管理画面【企業用】';

        }
    }

    function logout($role = ROLE_COMPANY)
    {
        switch ($role) {
            case ROLE_ADMIN:
                $this->session->set_userdata('admin', '');
                redirect('admin/login');
                break;
            case ROLE_COMPANY:
                $this->session->set_userdata('company', '');
                redirect('login');
                break;
        }
        //$this->session->sess_destroy ();
    }

    function _login_check($role = ROLE_GUEST)
    {
        if ($role == ROLE_GUEST) return true;
        switch ($role) {
            case ROLE_ADMIN:
                $this->user = $this->session->userdata('admin');
                if (!empty($this->user)) {
                    $this->header['user'] = $this->user;
                    return true;
                }
                break;
            case ROLE_STAFF:
                $this->staff = $this->session->userdata('staff');
                if (!empty($this->staff)) {
                    $this->header['staff'] = $this->staff;
                    return true;
                }
                break;
        }

        return false;
    }

    function _load_view_only($viewName = "")
    {

        $this->load->view($viewName, $this->data);
    }

    function _load_view($viewName = "", $prefix = '')
    {

        $this->load->view($prefix . 'includes/header', $this->header);
        $this->load->view($viewName, $this->data);
        $this->load->view($prefix . 'includes/footer', $this->footer);
    }

    function _load_view_admin($viewName = "")
    {

        $this->load->view('admin/includes/header', $this->header);
        $this->load->view($viewName, $this->data);
        $this->load->view('admin/includes/footer', $this->footer);
    }

    function _search_url($text)
    {
        $index = strpos($text, 'http://');
        if ($index !== FALSE) {
            $prefix = substr($text, 0, $index);
            $real_url = substr($text, $index);
            $ref_url = filter_var($real_url, FILTER_SANITIZE_URL);
            $href_url = str_replace($ref_url, ('<a href="' . $ref_url . '">' . $ref_url . '</a>'), $real_url);
            return $prefix . " " . $href_url;
        } else {
            $index = strpos($text, 'https://');
            if ($index !== FALSE) {
                $prefix = substr($text, 0, $index);
                $real_url = substr($text, $index);
                $ref_url = filter_var($real_url, FILTER_SANITIZE_URL);
                $href_url = str_replace($ref_url, ('<a href="' . $ref_url . '">' . $ref_url . '</a>'), $real_url);
                return $prefix . " " . $href_url;
            }
        }
        return $text;
    }

    function loadOrganByStaff($staff){
        $auth_id = $staff['staff_auth'];
        $staff_id = $staff['staff_id'];
        $company_id = $staff['company_id'];
        $organ_list = [];
        if ($auth_id==4){
            $organ_list = $this->organ_model->getListByCond([]);
        }
        if ($auth_id==3){
            $organ_list = $this->organ_model->getListByCond(['company_id'=>$company_id]);
        }
        if ($auth_id>=1 && $auth_id<3){
            $this->load->model('staff_organ_model');
            $organ_list = $this->staff_organ_model->getOrgansByStaff($staff_id);
        }

        return $organ_list;

    }
    protected function _call_api($api_url)
    {
        $headers = array(
            'Content-Type:application/json'
        );

        $fields = $this->input->post();
        ///////////////////// get jobs/////////////////

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            return array();
        } else {
            // check the HTTP status code of the request
            $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($resultStatus != 200) {
                return array();
            }
            $result_array = (array)json_decode($result);
            return $result_array;
        }

    }

    protected function debug($val)
    {
        echo '<pre/>';
        print_r($val);
        die;
    }

    protected function get_wix_url($wix_api_domain)
    {
        return 'https://' . $wix_api_domain . '.wixanswers.com/api/v1/';
    }

    protected function wix_get_token($wix_api_domain, $wix_api_key = '', $wix_api_secret = '')
    {
        $token = $this->session->userdata('wix_token');
        if (!empty($token)) return $token;

        if (empty($wix_api_domain) || empty($wix_api_key) || empty($wix_api_secret)) return false;

        $url = $this->get_wix_url($wix_api_domain) . 'accounts/token';

        $headers = array(
            "Accept: application/json",
            "Content-Type: application/json; charset=utf-8",
        );
        $post_data = array(
            'keyId' => $wix_api_key,
            'secret' => $wix_api_secret,
        );

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);


        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));

//for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($resp);
        if ($result && property_exists($result, 'token')) {
            $this->session->set_userdata('wix_token', $result->token);
            return $result->token;
        }
        return false;
    }

    protected function wix_analytics($wix_api_domain, $wix_api_key, $wix_api_secret, $start_date, $end_date)
    {
//POST https://seveneleven.wixanswers.com/api/v1/analytics/article/mostRead
//Example payload:
//{
//    "startDate": "2021-06-26",
//    "endDate": "2021-06-26",
//    "locale": "ja",
//    "histogramInterval": 2,
//    "pageSize": 200
//}
//compareEndDate: "2021-05-31"
//compareStartDate: "2021-05-17"
//endDate: "2021-06-15"
//histogramInterval: 2
//locale: "ja"
//pageSize: 10
//sourceId: "9ce86896-8031-46fc-8eaa-cfedfdb80527"
//sourceType: 1
//startDate: "2021-06-01"


        $token = $this->wix_get_token($wix_api_domain, $wix_api_key, $wix_api_secret);

        $url =  $this->get_wix_url($wix_api_domain) . 'analytics/article/mostRead';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer " . $token,
            "Content-Type: application/json; charset=utf-8",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $post_data = array(
            'locale' => 'ja',
            'startDate' => $start_date,
            'endDate' => $end_date,
            'histogramInterval' => 2,
            'pageSize' => 200,
        );

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));

//for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

        $result = (array)json_decode($resp);

        if (empty($result)) {
            $token = $this->session->unset_userdata('wix_token');
            return array();
        }
        return $result;

    }

    protected function wix_get_user($guid, $wix_api_domain, $wix_api_key, $wix_api_secret)
    {
        $token = $this->wix_get_token($wix_api_domain, $wix_api_key, $wix_api_secret);
        $wix_api_url = 'https://' . $wix_api_domain . '.wixanswers.com/api/v1/';

        $url = $wix_api_url . 'users/'.$guid;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer " . $token,
            "Content-Type: application/json; charset=utf-8",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

//for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

        $result = (array)json_decode($resp);
        if (empty($result)) {
            $token = $this->session->unset_userdata('wix_token');
            return array();
        }
        $this->debug($result);
        return $result;
    }

    protected function wix_article_list($search_text, $page, $page_count, $wix_api_domain, $wix_api_key, $wix_api_secret)
    {
        $wix_api_url = 'https://' . $wix_api_domain . '.wixanswers.com/api/v1/';
        $token = $this->wix_get_token($wix_api_domain, $wix_api_key, $wix_api_secret);

        $url = $wix_api_url . 'articles/search/admin';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer " . $token,
            "Content-Type: application/json; charset=utf-8",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $post_data = array(
            'locale' => 'ja',
            'text' => $search_text,
            "spellcheck" => true,
            "statuses" => [10],//Published
            "types" => [100],//Article
//            "fromLastPublishDate" => strtotime('2021-05-22 00:00:00'),
//            "fromLastUpdateDate" => strtotime('2021-05-22 00:00:00')*1000,
//            "toLastUpdateDate" =>  strtotime('2021-06-22 00:00:00')*1000,
            "page" => $page,
            "pageSize" => $page_count,
            "sortType" => 100
        );

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));

//for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

        $result = (array)json_decode($resp);

        if (empty($result)) {
            $token = $this->session->unset_userdata('wix_token');
            return array();
        }
        return $result;
    }

    protected function wix_search($search_text, $wix_api_domain, $wix_api_key, $wix_api_secret)
    {
        $wix_api_url = 'https://' . $wix_api_domain . '.wixanswers.com/api/v1/';
        $token = $this->wix_get_token($wix_api_domain, $wix_api_key, $wix_api_secret);

        $url = WIX_API_URL . 'articles/search';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer " . $token,
            "Content-Type: application/json; charset=utf-8",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $post_data = array(
            'locale' => 'ja',
            'text' => $search_text,
            "spellcheck" => true,
            "page" => 1,
            "pageSize" => 5,
            "sortType" => 100
        );

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));

//for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

        $result = (array)json_decode($resp);

        if (isset($result['items'])) {
            $list = array();
            foreach ($result['items'] as $item) {
                $item = (array)$item;

                $row = array();
                $row['id'] = $item['id'];
                $row['title'] = $item['title'];
                $row['content'] = $item['content'];
                $list[] = $row;
            }

            return $list;

        }
    }

    protected function wix_search_savedreply($search_text, $wix_api_domain, $wix_api_key, $wix_api_secret)
    {
        $wix_api_url = 'https://' . $wix_api_domain . '.wixanswers.com/api/v1/';
        $token = $this->wix_get_token($wix_api_domain, $wix_api_key, $wix_api_secret);
        if (empty($token)) return '';

        $url = WIX_API_URL . 'savedReplies/search';

        try {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
                "Accept: application/json",
                "Authorization: Bearer " . $token,
                "Content-Type: application/json; charset=utf-8",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $post_data = array(
                'locale' => 'ja',
                'text' => $search_text,
                'spellcheck' => false,
                'pageSize' => 10
            );

            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));

//for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);

            // Check the return value of curl_exec(), too
            if ($resp === false) {
                throw new Exception(curl_error($curl), curl_errno($curl));
            }

            curl_close($curl);

            $result = (array)json_decode($resp);
            if (empty($result) || !isset($result['items'])) {
                $token = $this->session->unset_userdata('wix_token');
                return '';
            }
            foreach ($result['items'] as $item) {
                $item = (array)$item;
//                $pos = mb_strpos( $item['title'],$search_text );
//                var_dump($pos);
                if ($item['title'] == $search_text) {
                    return $item['content'];
                }
            }
            return '';
        } catch (Exception $e) {
            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);

        }

        return '';
    }


    /**
     * This function used provide the pagination resources
     * @param {string} $link : This is page link
     * @param {number} $count : This is page count
     * @param {number} $perPage : This is records per page limit
     * @return {mixed} $result : This is array of records and pagination data
     */
    function _paginationCompress($link, $count, $perPage = 10, $segment = SEGMENT)
    {
        $this->load->library('pagination');

        $config ['base_url'] = base_url() . $link;
        $config ['total_rows'] = $count;
        $config ['uri_segment'] = $segment;
        $config ['per_page'] = $perPage;
        $config ['num_links'] = 5;
        $config ['full_tag_open'] = '<nav><ul class="pagination">';
        $config ['full_tag_close'] = '</ul></nav>';
        $config ['first_tag_open'] = '<li class="arrow">';
        $config ['first_tag_close'] = '</li>';
        $config ['prev_tag_open'] = '<li class="arrow">';
        $config ['prev_tag_close'] = '</li>';
        $config ['next_tag_open'] = '<li class="arrow">';
        $config ['next_tag_close'] = '</li>';
        $config ['cur_tag_open'] = '<li class="active"><a href="#">';
        $config ['cur_tag_close'] = '</a></li>';
        $config ['num_tag_open'] = '<li>';
        $config ['num_tag_close'] = '</li>';
        $config ['last_tag_open'] = '<li class="arrow">';
        $config ['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);
        $page = $config ['per_page'];
        $segment = $this->uri->segment($segment);

        return array(
            "page" => $page,
            "segment" => $segment
        );
    }

    function load_view_with_menu($viewName = "", $prefix = "")
    {
        $this->header['contents'] = $this->load->view($viewName, $this->data, true);
        $this->load->view($prefix . 'includes/withmenu', $this->header);
    }
}
