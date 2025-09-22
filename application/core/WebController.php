<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class WebController
 */
class WebController extends CI_Controller
{
    public $data;
    public $user;
    public $user_id;

    /**
     * Class constructor
     *
     * @return    void
     */
    public function __construct($role = ROLE_GUEST)
    {
        parent::__construct();

        if (!$this->_login_check($role)) {
            redirect('login');
        }

    }

    function _login_check($role = ROLE_GUEST)
    {
        $company = $this->session->userdata('company');
        $customer = $this->session->userdata('customer');
        return true;
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

    protected function wix_article_list($search_text, $page, $page_count, $wix_api_domain, $wix_api_key, $wix_api_secret)
    {
        $token = $this->wix_get_token($wix_api_domain, $wix_api_key, $wix_api_secret);

        $url = $this->get_wix_url($wix_api_domain) . 'articles/search';

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

        return $result;
    }

    protected function wix_search($search_text, $wix_api_domain, $wix_api_key, $wix_api_secret)
    {
        $token = $this->wix_get_token($wix_api_domain, $wix_api_key, $wix_api_secret);

        $url = $this->get_wix_url($wix_api_domain) . 'articles/search';

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
        $token = $this->wix_get_token($wix_api_domain, $wix_api_key, $wix_api_secret);
        if (empty($token)) return '';

        $url = $this->get_wix_url($wix_api_domain) . 'savedReplies/search';

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
                'spellcheck' => true,
                "page" => 1,
                "pageSize" => 1,
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

    function _faq_sync($company_id){

        ini_set('max_execution_time', 0);
        $this->data['company'] = $this->company_model->get($company_id);

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
                        'company_id' => $company_id,
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
    }


    public function sendFireBaseMessage($type, $sender_id, $title, $body, $token, $badge){
        try {
            $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

            $notification = [
                'title' => $title,
                'body' => $body,
//                'icon' => 'myIcon',
                'sound' => 'default',
                'badge' => $badge
            ];
            $extraNotificationData = ["message" => $notification, "type" => $type, "sender_id" =>$sender_id];
            $fcmNotification = [
                'to' => $token, //single token
                'notification' => $notification,
                'data' => $extraNotificationData
            ];
            $headers = [
                'Authorization: key=AAAA7-7YI6E:APA91bF5qh5xiYllQINttSsBnXdIsBXmSu4fIF5bZ4UDWhdmVuAsdWRNSOjbyFPTyABVOlU9N4JCOvQvbn42TVK0DAfPQEHgWsFiQD5X2XA_VqWTLOOk2_PFXj_oi8egjRumDIxDrYH_',
                'Content-Type: application/json'
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fcmUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
            $result = curl_exec($ch);
            curl_close($ch);

        }catch(Exception $e){
            return false;
        }

        return true;
    }

    public function sendMailMessage($title, $body, $receive_mail = 'devtoworld@gmail.com'){
        try {
                $config = array(
                    'protocol' => 'smtp', // 'mail', 'sendmail', or 'smtp'
                    'smtp_host' => 'mail.silverfeathers.net',
                    'smtp_port' => 587,
                    'smtp_user' => 'admin@silverfeathers.net',
                    // 'smtp_pass' => '1#TQUr*zX-gF]Xx)',
                    'smtp_pass' => 'M_W_0~OmQ6B4',
                );
           /* $config = array(
                'protocol' => 'smtp', // 'mail', 'sendmail', or 'smtp'
                'smtp_host' => 'ssl://email-smtp.ap-northeast-1.amazonaws.com',
                'smtp_port' => 465,
                'smtp_user' => 'AKIASUAM6A5IO2FDEBI4',
                'smtp_pass' => 'BNvvY3Y2/SbdY4ciUYaoXMtfAWEACY6CCG3xfeNEQFYe',
				'mailtype'  => 'html', 
				'charset'   => 'utf-8'
            );*/

            $this->load->library('email');

            $this->email->initialize($config);

            $this->email->from('admin@silverfeathers.net', 'Visit System');
            $this->email->to($receive_mail);
            $this->email->subject($title);
            $this->email->message($body);
			$this->email->set_newline("\r\n");
            $this->email->send();

        }catch (Exception $e){
            return false;
        }
    }

    public function sendNotifications($n_type, $title, $content, $sender_id, $receiver_id, $receiver_type,$order_id=''){

//        $notification_type = 0;
//        if ($n_type=='message') $notification_type = 1;
//        if ($n_type=='shift_require') $notification_type = 2;
//        if ($n_type=='add_point_request') $notification_type = 3;
//        if ($n_type=='shift_request') $notification_type = 4;
//        if ($n_type=='reserve') $notification_type = 5;
//        if ($n_type=='shift_accept') $notification_type = 6;

        $isFcm = false;
        $this->load->model('device_token_model');
        $this->load->model('user_model');
        $mail_address = "";
        $company_id = '';
        if ($receiver_type=='1'){
            $staff_data = $this->device_token_model->getRecordByCondition(['staff_id'=>$receiver_id]);
            $staff = $this->staff_model->getFromId($receiver_id);
            if (empty($staff_data)) return $isFcm;
            $token_data = $staff_data['device_token'];
            $mail_address = $staff['staff_mail'];
            $company_id = $staff['company_id'];
        }
        
        if ($receiver_type=='2'){
            $user = $this->user_model->getFromId($receiver_id);
            if (empty($user)) return $isFcm;
            $token_data = $user['user_device_token'];
            $mail_address = $user['user_email'];
            $company_id = $user['company_id'];
        }

        $this->load->model('company_model');
        $company = $this->company_model->getFromId($company_id);
        if (!empty($token_data)){
            $this->load->model('notification_model');
            $cond = [];
            $cond['receiver_type'] = $receiver_type;
            $cond['receiver_id'] = $receiver_id;
            $cond['notification_type'] = $n_type;
            $notification = $this->notification_model->getRecordByCond($cond);
            if (empty($notification)){
                $data = array(
                    'receiver_type' => $receiver_type,
                    'receiver_id' => $receiver_id,
                    'notification_type' => $n_type,
                    'badge_count' => '1',
                );
                if (!empty($order_id)) {
                    $data['order_id'] = $order_id;
                }
                $this->notification_model->insertRecord($data);

            }else{
                $count = (empty($notification['badge_count'])? 0: $notification['badge_count']) + 1;
                $notification['badge_count'] = $count;

                $this->notification_model->updateRecord($notification, 'id');
            }

            $badge = $this->notification_model->getBageCount($receiver_id, $receiver_type);

            if (isset($company) && $company['is_push']==1){
                $isFcm = $this->sendFireBaseMessage($n_type, $sender_id, $title, $content, $token_data, $badge);
            }
            if (isset($company) && $company['is_mail']==1){
                $isMail = $this->sendMailMessage($title, $content, $mail_address);
            }
        }

        return $isFcm;
    }

    public function clacPersonRate($staff_id, $year, $month){
        $this->load->model('staff_model');
        $staff = $this->staff_model->getFromId($staff_id);
        $sum = 0;
        if (empty($staff['staff_entering_date'])){
//            $sum += 0;
        }else{
            $startDateTime = new DateTime($staff['staff_entering_date'].'-01');
            $endDateTime = new DateTime($year.'-'.$month.'-01');

            $dateDiff = date_diff($startDateTime, $endDateTime);
            if ($dateDiff->y >= 5){
                $sum += 1.25;
//                $year_grade = 4;
            }else if($dateDiff->y >= 2){
                $sum += 1.22;
//                $year_grade = 3;
            }else if($dateDiff->y >= 1){
                $sum += 1.21;
//                $year_grade = 2;
            }else{
                $sum += 1.1;
//                $year_grade = 1;
            }
        }

        if (empty($staff['staff_grade_level'])){
//            $sum += 0;
        }else {
            if($staff['staff_grade_level'] == '1'){
                $sum += 0.02;
            }else if ($staff['staff_grade_level'] == '2'){
                $sum += 0.02;
            }
        }

        if (empty($staff['staff_national_level'])){
//            $sum += 0;
        }else {
            if($staff['staff_national_level'] == '1'){
                $sum += 0.01;
            }
        }

        return number_format($sum, 2);

    }

    public function getOrgansByStaffPermission($staff_id){
        $staff = $this->staff_model->getFromId($staff_id);
        $organs = [];
        if (empty($staff)) return $organs;

        $auth = empty($staff['staff_auth']) ? 1 : $staff['staff_auth'];

        if($auth<4){
            $cond['staff_id'] = $staff_id;
            $organs = $this->staff_organ_model->getOrgansByStaff($staff_id);
        }
        if($auth==4){
            $cond = [];
            if ($auth<5) $cond['company_id'] = $staff['company_id'];
            $organs = $this->organ_model->getListByCond(['company_id'=>$staff['company_id']]);
        }
        if($auth>4){
            $organs = $this->organ_model->getListByCond([]);
        }

        return $organs;
    }

    public function updateStampRanking($user_id){
        $user = $this->user_model->getFromId($user_id);
        if (empty($user)) return false;

        $company_id = $user['company_id'];
        $grade = $user['user_grade'];

        $this->load->model('rank_model');
        $this->load->model('stamp_model');
        $rank_data = $this->rank_model->getRankRecord(['company_id' => $company_id, 'rank_level' => $grade]);
        if (empty($rank_data)) return false;

        $user_stamps = $this->stamp_model->getStampList(['company_id' => $company_id, 'user_id' => $user_id, 'use_flag' => 0]);
        if (count($user_stamps) >= $rank_data['max_stamp']){
            $next_grade = intval($grade) + 1;
            $update_rank_data = $this->rank_model->getRankRecord(['company_id' => $company_id, 'rank_level' => $next_grade]);

            if (empty($update_rank_data)){
                $max_rank = $this->rank_model->getMaxRank($company_id);
                if ($grade == $max_rank && $max_rank>0){
                    $user['user_grade'] = 1;
                    $user['user_is_gold']++;
                }
            }else{
                $user['user_grade'] = $update_rank_data['rank_level'];
            }            

	    //$user['user_grade'] = $update_rank_data['rank_level'];
            $this->user_model->updateRecord($user, 'user_id');
            $remove_count = 0;
            foreach ($user_stamps as $user_stamp){
                $user_stamp['use_flag'] = 1;
                $this->stamp_model->updateRecord($user_stamp, 'stamp_id');
				$remove_count++;
                if ($remove_count >= $rank_data['max_stamp']) break;
            }
            return true;
        }

        return false;

    }

    public function addItemByStamp($user_id){
        $user = $this->user_model->getFromId($user_id);
        if (empty($user)) return false;
        $company_id = $user['company_id'];
        $grade = $user['user_grade'];

        $this->load->model('rank_model');
        $this->load->model('stamp_model');
        $this->load->model('rank_prefer_model');
        $this->load->model('user_coupon_model');
        $this->load->model('user_stamp_care_model');

        $rank_data = $this->rank_model->getRankRecord(['company_id' => $company_id, 'rank_level' => $grade]);
        if (empty($rank_data)) return false;

        $user_stamps = $this->stamp_model->getStampList(['company_id' => $company_id, 'user_id' => $user_id, 'use_flag' => 0]);
        $stampCnt = count($user_stamps );

	// if user grade is gold, check old Stamp
		if ($user['user_is_gold']>0) {
			$prefers = $this->rank_prefer_model->getAllPrefers($company_id);
			
			foreach ($prefers as $prefer){
				$old_care = $this->user_stamp_care_model->getDataByParam(['user_id' => $user_id, 'rank_prefer_id' => $prefer['rank_prefer_id'], 'is_gold' => 0]);
				if (empty($old_care)) {
					$type = $prefer['type'];
					if ($type==1){
						$menu_id = $prefer['menu_id'];
					}elseif ($type==2){
						$coupon_id = $prefer['coupon_id'];
						$this->user_coupon_model->insertRecord([
							'user_id' => $user_id,
							'coupon_id' => $coupon_id,
							'use_flag' => 1,
						]);
					}
					
					$this->user_stamp_care_model->insertRecord([
							'user_id' => $user_id,
							'is_gold' => 0,
							'rank_prefer_id' => $prefer['rank_prefer_id'],
							'visible' => 1,
						]);
				}

			}
		}

		$prefers = $this->rank_prefer_model->getAvailablePrefers($company_id, $rank_data['rank_level'], $stampCnt);
//        $prefers = $this->rank_prefer_model->getListByCond(['rank_id'=>$rank_data['rank_id'], 'max_stamp_cnt'=>$stampCnt]);

		foreach ($prefers as $prefer){
			$old_care = $this->user_stamp_care_model->getDataByParam(['user_id' => $user_id, 'rank_prefer_id' => $prefer['rank_prefer_id'], 'is_gold' => $user['user_is_gold']]);
			if (empty($old_care)) {
				$type = $prefer['type'];
				if ($type==1){
					$menu_id = $prefer['menu_id'];
				}elseif ($type==2){
					$coupon_id = $prefer['coupon_id'];
					$this->user_coupon_model->insertRecord([
						'user_id' => $user_id,
						'coupon_id' => $coupon_id,
						'use_flag' => 1,
					]);
				}
				
				$this->user_stamp_care_model->insertRecord([
						'user_id' => $user_id,
						'is_gold' => $user['user_is_gold'],
						'rank_prefer_id' => $prefer['rank_prefer_id'],
						'visible' => 1,
					]);
			}

        }
        return true;
    }


    /*
     *  Added By Katsumoto 2023/08/29
     *  get FromTime And ToTime of Organ In Select Date (With min Value)
     *
     */
    public function getOrganTimeByDate($organ_id, $select_date){

        $this->load->model('organ_time_model');
        $this->load->model('organ_special_time_model');

        $weekday = date('N', strtotime($select_date));

        $organ_times = $this->organ_time_model->getListByCond(['organ_id'=>$organ_id, 'weekday' => $weekday]);
        $organ_special_times = $this->organ_special_time_model->getListByCond(['organ_id'=>$organ_id, 'select_date' => $select_date]);

        $from_time = 0;
        $to_time = 0;
        $is_set_time = false;

        if (!empty($organ_times)){
            $i = 0;
            foreach ($organ_times as $timeRow){
                $from_s = explode(":", $timeRow['from_time']) ;
                $to_s = explode(":", $timeRow['to_time']) ;
                $from = $from_s[0] * 60 + $from_s[1];
                $to = $to_s[0] * 60 + $to_s[1];

                $i++;
                if ($i==1){
                    $from_time = $from;
                    $to_time = $to;
                    $is_set_time = true;
                }else{
                    if ($from<$from_time) $from_time = $from;
                    if ($to>$to_time) $to_time = $to;
                }
            }
        }

        if (!empty($organ_special_times)){
            $i = 0;
            foreach ($organ_special_times as $timeRow){
                if ($timeRow['from_time']<($select_date. " 00:00:00")){
                    $r_from = "00:00";
                }else{
                    $r_from = substr($timeRow['from_time'], 11,5) ;
                }

                if ($timeRow['to_time']>=($select_date. " 23:59:59")){
                    $r_to = "24:00";
                }else{
                    $r_to = substr($timeRow['to_time'], 11,5) ;
                }

                $from_s = explode(":", $r_from) ;
                $to_s = explode(":", $r_to) ;
                $from = $from_s[0] * 60 + $from_s[1];
                $to = $to_s[0] * 60 + $to_s[1];

                $i++;
                if ($i==1 && !$is_set_time){
                    $from_time = $from;
                    $to_time = $to;
                    $is_set_time = true;
                }else{
                    if ($from<$from_time) $from_time = $from;
                    if ($to>$to_time) $to_time = $to;
                }
            }
        }

        $from_time = intval($from_time/5) * 5;
        $to_time = intval($to_time/5) * 5;

        return ['from'=>$from_time, 'to'=>$to_time];
    }

    public function syncEparkShift($organ_id, $staff_id, $select_date){
        $this->load->helper('epark');
        $epark_helper = new Epark_helper(2);
        $r = $epark_helper->updateShiftToEpark($organ_id, $staff_id, $select_date);
    }
}
