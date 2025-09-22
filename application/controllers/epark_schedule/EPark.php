<?php 
// if (!defined('BASEPATH')) exit('No direct script access allowed');

class EPark extends CI_Controller
{
    const API_BASE                          = "https://dev.peakmanager.com";
    const AUTH                              = EPark::API_BASE."/mi_api/app/SPConnect/auth";
    const GET_HOME                          = EPark::API_BASE."/mi_api/v1/memberapp/get_home";
    const TELMAIL_CHECK                     = EPark::API_BASE."/mi_api/v1/memberapp/telmail_check";
    const LOGIN_TELMAIL                     = EPark::API_BASE."/mi_api/v1/memberapp/login_by_telmail";
    const TELMAIL_CERTI                     = EPark::API_BASE."/mi_api/v1/memberapp/telmail_certification";
    const CERTIFICATION_CONFIRM             = EPark::API_BASE."/mi_api/v1/memberapp/certification_confirm";
    const SHOP_SEARCH                       = EPark::API_BASE."/mi_api/v1/memberapp/shop_search";
    const STORE_INFORMATION                 = EPark::API_BASE."/mi_api/v1/shop/getinfo";
    const MENU_SEARCH                       = EPark::API_BASE."/mi_api/v1/menu/search";
    const GET_MENU_LIST                     = EPark::API_BASE."/mi_api/v1/menu/list";
    const GET_STAFF_LIST                    = EPark::API_BASE."/mi_api/v1/staff/search";
    const GET_SCHEDULE                      = EPark::API_BASE."/mi_api/v1/schedules/get";
    const GET_COURSE_BY_TYPE                = EPark::API_BASE."/mi_api/v1/memberapp/get_course_by_type";
    const ADD_COURSE_LIST                   = EPark::API_BASE."/mi_api/v1/memberapp/add_courses_list";
    const ADD_OPTION_LIST                   = EPark::API_BASE."/mi_api/v1/memberapp/add_options_list";
    const BOOKING_REGISTER                  = EPark::API_BASE."/mi_api/v1/memberapp/booking_register";
    const AGREEMENT_INFO                    = EPark::API_BASE."/mi_api/v1/memberapp/agreement_info";
    const BOOKING_CANCEL                    = EPark::API_BASE."/mi_api/v1/booking/cancel";
    const ADD_CUSTOMER                      = EPark::API_BASE."/mi_api/v1/customers/add";
    const GET_CUSTOMER                      = EPark::API_BASE."/mi_api/v1/customers/search";
    const COUPON_LIST                       = EPark::API_BASE."/mi_api/v1/memberapp/coupon_list";
    const COUPON_DETAIL                     = EPark::API_BASE."/mi_api/v1/memberapp/coupon_detail";
    const USE_COUPON                        = EPark::API_BASE."/mi_api/v1/memberapp/use_coupon";
    const NOTIFICATION_LIST                 = EPark::API_BASE."/mi_api/v1/memberapp/notification_list";
    const NOTIFICATION_DETAIL               = EPark::API_BASE."/mi_api/v1/memberapp/notification_detail";
    const NEWPM_GET_COUPON                  = EPark::API_BASE."/mi_api/v1/mypage/newpm_getcoupon";
    const NEWPM_HISTORY                     = EPark::API_BASE."/mi_api/v1/mypage/newpm_history";
    const MODIFY_CUSTOMER                   = EPark::API_BASE."/mi_api/v1/mypage/ajax_modify_customer";
    const GET_CUSTOMER_TICKET_HISTORY       = EPark::API_BASE."/mi_api/v1/mypage/get_customer_ticket_history";
    const GET_STAMP                         = EPark::API_BASE."/mi_api/v1/memberapp/get_stamp";
    const GET_PROMOTION_COUPON              = EPark::API_BASE."/mi_api/v1/memberapp/get_promotion_coupon";
    const PROMOTION_COUPON_DETAIL           = EPark::API_BASE."/mi_api/v1/memberapp/promotion_coupon_detail";
    const GET_SIMPLE_HISTORY                = EPark::API_BASE."/mi_api/v1/mypage/get_simple_history";
    const EPARK_LINK                        = EPark::API_BASE."/mi_api/v1/memberapp/epark_link";
    const CUSTOMER_LOGOUT                   = EPark::API_BASE."/mi_api/v1/customers/logout";
    const GET_CARD_MEMBER                   = EPark::API_BASE."/mi_api/v1/memberapp/get_card_member";
    const SET_EMAIL_SETTING                 = EPark::API_BASE."/mi_api/v1/mypage/ajax_set_setting";
    const GET_MAIL_MAGAZINE_SETTING         = EPark::API_BASE."/mypage/get_mail_magazine_settings";
    const GET_MAIL_ADDRESS_CHANGE           = EPark::API_BASE."/mi_api/v1/mypage/ajax_mailaddress_change";
    const PM_EMAIL_EXISTS                   = EPark::API_BASE."/mi_api/v1/online/pm_email_exists";
    const DISPLAY_MEMBER_POINT_INFO         = EPark::API_BASE."/mi_api/v1/mypage/display_member_point_info";
    const GET_ACCESS_TOKEN                  = EPark::API_BASE."/mi_api/v1/customers/get_access_token";
    const GET_COMMANY_INFORMATION           = EPark::API_BASE."/mi_api/v1/company/getinfo";
    const SAVE_APP_OPERATION_LOGS           = EPark::API_BASE."/mi_api/v1/memberapp/save_logs";
    const MEDIQU_LOGIN                      = EPark::API_BASE."/mi_api/medique/users/login";
    const MEDIQU_SHIFT_LIST                 = EPark::API_BASE."/mi_api/medique/master/shift_list";
    const MEDIQU_BOOKING_LIST               = EPark::API_BASE."/mi_api/medique/ibooking/slist";
    const MEDIQU_PHONE_CHECK                = EPark::API_BASE."/mi_api/medique/master/telCheck";
    const MEDIQU_CUSTOMER_TAG               = EPark::API_BASE."/mi_api/medique/master/customerTags";
    const MEDIQU_CUSTOMER_DETAILS           = EPark::API_BASE."/mi_api/medique/customer/detail";
    const MEDIQU_CUSTOMER_SAVE              = EPark::API_BASE."/mi_api/medique/customer/save";
    const MEDIQU_CUSTOMER_SIGN_IMAGE        = EPark::API_BASE."/mi_api/medique/customer/signImageSave";
    const MEDIQU_GET_SETTINGS               = EPark::API_BASE."/mi_api/medique/master/getMediqueSettings";
    const MEDIQU_CUSTOMER_MAIL_CHANGE       = EPark::API_BASE."/mi_api/medique/customer/mailchange";

    public function __construct()
    {
        parent::__construct();

        $this->load->model('staff_model');
        $this->load->model('coupon_model');
        $this->load->model('user_coupon_model');
        $this->load->model('user_model');
        $this->load->model('company_model');
        $this->load->model('stamp_model');
        $this->load->model('rank_model');
        $this->load->model('rank_prefer_model');
    }

    public function curl_post($url, $params){
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

    public function authenticate(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://dev.peakmanager.com/users/login',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => '_method=POST&data%5BUser%5D%5Blogin%5D=ascreate-demo&data%5BUser%5D%5Bpassword%5D=!ascreate-demo',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
            'Cookie: CAKEPHP=dkjok8cip3q141qf1isv98qqmj'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        var_dump($response);
        die();
        


    }

    public function syncEpark(){
        $db = $this->staff_model->db;

        $now = "'".date('Y-m-d H:i:s')."'";
        $error = " --error ";

        $auth_id = "test";
        $auth_pass = "aHR74jfikfl";
        $company_id = "10306";



        $auth = ["auth_id"=>$auth_id, "auth_pass"=>$auth_pass];

        $auth_result = $this->curl_post(EPark::AUTH, $auth);
        if(empty($auth_result)){
            echo EPark::AUTH.$error;
            return;
        }

        $api_key = $auth_result["access_token_app"];

        echo $api_key;


        // $sql = "INSERT INTO epark_is_running (`created_at`) VALUES (_now);";
        // $sql = str_replace("_now", $now, $sql);

        // $is = $db->query($sql);
        // if(!$is){
        //     echo $sql." --error";
        //     return;
        // }
    
        // echo "Sync Epark Ok.";
    }
}

?>
