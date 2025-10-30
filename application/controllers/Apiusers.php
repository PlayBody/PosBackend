<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apiusers extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('group_user_model');
        $this->load->model('user_ticket_reset_setting_model');
        $this->load->model('user_ticket_model');
        $this->load->model('reserve_model');
        $this->load->model('history_table_model');
        $this->load->model('stamp_model');
    }

    public function getUserData(){
        $condition = $this->input->post('condition');
        $cond = json_decode($condition, true);
        if (!empty($cond['user_password'])) $cond['user_password'] = sha1($cond['user_password']);
        $user = $this->user_model->getOneByParam($cond);

        if (empty($user)){
            $results['isLoad'] = false;
        }else{
            $results['isLoad'] = true;
            $results['user'] = $user;
        }
        echo json_encode($results);
    }

    public function loadUserInfo(){
        $user_id = $this->input->post('user_id');
        if (empty($user_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $user = $this->user_model->getFromId($user_id);

        $groups = $this->group_user_model->getGroupsByUser($user_id);
        
        // Get user tickets with reset_count information
        $user_tickets = $this->user_ticket_model->getListByCond(['user_id'=>$user_id]);

        $ticket_reset = $this->user_ticket_reset_setting_model->getResetSetting(['user_id'=>$user_id]);

        if (!empty($groups)){
            $user['group'] = $groups[0];
            $user['group_name'] = $groups[0]['group_name'];
        } else {
            $user['group_name'] = '';
        }
        
        // Add reset_count from ticket id 31 if exists, otherwise from the first user ticket
        if (!empty($user_tickets)){
            // Check if ticket id 31 exists
            $ticket_31 = null;
            foreach ($user_tickets as $ticket) {
                if ($ticket['ticket_id'] == 31) {
                    $ticket_31 = $ticket;
                    break;
                }
            }
            
            // Use ticket 31's reset_count if found, otherwise use the first ticket
            if ($ticket_31) {
                $user['reset_count'] = $ticket_31['reset_count'];
            } else {
                $user['reset_count'] = $user_tickets[0]['reset_count'];
            }
        } else {
            $user['reset_count'] = '0';
        }
        
        $results['isLoad'] = true;
        $results['user'] = $user;
        $results['user_tickets'] = $user_tickets;
        $results['ticket_reset'] = empty($ticket_reset) ? null : $ticket_reset ;

        echo json_encode($results);
    }


    public function loadUserFromQrNo(){
        $user_no = $this->input->post('user_no');

        if (empty($user_no)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $user = $this->user_model->getRecordByCond(['user_no'=>$user_no]);

        if (empty($user)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $results['isLoad'] = true;
        $results['user'] = $user;

        echo json_encode($results);
    }

    public function loadUserWithGroupList(){
        $company_id = $this->input->post('company_id');
        $group_id = $this->input->post('group_id');
        if (empty($company_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $users = $this->user_model->getUsersByCond(['company_id'=>$company_id]);

        $group_users = [];
        if (!empty($group_id)){
            $group_users = $this->group_user_model->getUsersByGroupGroup($group_id);
        }

        $results['isLoad'] = true;
        $results['users'] = $users;
        $results['group_users'] = $group_users;

        echo json_encode($results);
    }

    public function loadUserInGroupList(){
        $company_id = $this->input->post('company_id');
        $group_id = $this->input->post('group_id');
        if (empty($company_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $users = $this->user_model->getUserListInSelectGroup($company_id, $group_id);

        $results['isLoad'] = true;
        $results['users'] = $users;

        echo json_encode($results);
    }

    public function loadUserList(){
        $company_id = $this->input->post('company_id');
        $condition = $this->input->post('condition');

        if (empty($company_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }
		
	$cond=[];
	if(!empty($condition)){
		$cond = json_decode($condition , true);
	}
	$cond['company_id'] = $company_id;

        $users = $this->user_model->getUsersByCond($cond);

        $result_user = [];
        foreach ($users as $user){
			if(!empty($cond['last_visit_date'])){
				$visit_data = $this->history_table_model->getMaxEnteringDate($user['user_id']);
				if (empty($visit_data) || $visit_data['last_visit_date']<$cond['last_visit_date']){
					continue;
				}
			}
            $tmp = $user;
            $reserves = $this->reserve_model->getDataByParam(['user_id'=>$user['user_id']]);
            $tmp['reserve_count'] = empty($reserves) ? 0 : count($reserves);

            $visits = $this->history_table_model->getDataByParam(['user_id'=>$user['user_id']]);
            $tmp['visit_count'] = empty($visits) ? 0 : count($visits);
            $result_user[] = $tmp;
        }
        $results['isLoad'] = true;
        $results['users'] = $result_user;

        echo json_encode($results);
    }

    public function updateUserTicket(){
        $user_id = $this->input->post('user_id');
        $ticket_count = $this->input->post('ticket_count');

        $user = $this->user_model->getFromId($user_id);

        if (!empty($user)){
            $user['user_ticket'] = $ticket_count;
            $this->user_model->updateRecord($user, 'user_id');
        }

        $results['is_update'] = true;

        echo json_encode($results);
    }

    public function saveUserInfo(){
        $company_id = $this->input->post('company_id');
        $user_id = $this->input->post('user_id');

        $user_email = $this->input->post('user_email');

        $results['err_type'] = '';
        if (!empty($user_email)){
            if(empty($user_id)){
                $isEmailExist = $this->user_model->checkEmailExists($user_email, $company_id);
            } else {
                $isEmailExist = $this->user_model->checkEmailExists($user_email, $company_id, $user_id);
            }

            if($isEmailExist){
                $results['isSave'] =false;
                $results['err_type'] = 'mail_exist';
                echo json_encode($results);
                return;

            }
        }

        if (empty($user_id)){

            $user_code = $this->generateUserCode();

            $user = array(
                'company_id' => $company_id,
                'user_no' => $user_code,
                'user_grade' => '1',
                'user_is_gold' => empty($this->input->post('user_is_gold')) ? 0 : $this->input->post('user_is_gold'),
                'user_qrcode' => $this->generateUserQRCode($user_code, $company_id),
                'user_first_name' => empty($this->input->post('user_first_name')) ? null : $this->input->post('user_first_name'),
                'user_last_name' => empty($this->input->post('user_last_name')) ? null : $this->input->post('user_last_name'),
                'user_nick' => $this->input->post('user_nick'),
                'user_email' => empty($this->input->post('user_email')) ? null : $this->input->post('user_email'),
                'user_tel' => empty($this->input->post('user_tel')) ? null : $this->input->post('user_tel'),
                'user_sex' => empty($this->input->post('user_sex')) ? null : $this->input->post('user_sex'),
                'user_birthday' => empty($this->input->post('user_birthday')) ? null : $this->input->post('user_birthday'),
                'user_ticket' => 0,
                'user_device_token' => empty($this->input->post('user_device_token')) ? '' : $this->input->post('user_device_token'),
                'user_password' => empty($this->input->post('user_password')) ? null : sha1($this->input->post('user_password')),
                'visible' => 1,
            );
            $user_id = $this->user_model->insertRecord($user);
        }else{
            $user = $this->user_model->getFromId($user_id);

			$old_grade = $user['user_grade'];

			$cond['user_id'] = $user_id;
			$cond['use_flag'] = 0;
			$stamps = $this->stamp_model->getStampList($cond);
			$old_stamp_cnt = count($stamps);


            if (empty($user['user_no'])){
                $user['user_no'] = $this->generateUserCode();
                $user['user_qrcode'] = $this->generateUserQRCode($user['user_no'], $company_id);
            }

            if (!empty($this->input->post('user_first_name'))) $user['user_first_name'] = $this->input->post('user_first_name');
            if (!empty($this->input->post('user_last_name'))) $user['user_last_name'] = $this->input->post('user_last_name');
            if (!empty($this->input->post('user_nick'))) $user['user_nick'] = $this->input->post('user_nick');
            if (!empty($this->input->post('user_email'))) $user['user_email'] = $this->input->post('user_email');
            if (!empty($this->input->post('user_tel'))) $user['user_tel'] = $this->input->post('user_tel');
            if (!empty($this->input->post('user_sex'))) $user['user_sex'] = $this->input->post('user_sex');
            if (!empty($this->input->post('user_birthday'))) $user['user_birthday'] = $this->input->post('user_birthday');
            if (!empty($this->input->post('user_ticket'))) $user['user_ticket'] = $this->input->post('user_ticket');
            if (!empty($this->input->post('user_comment'))) $user['user_comment'] = $this->input->post('user_comment');
			if (!empty($this->input->post('user_grade'))) $user['user_grade'] = $this->input->post('user_grade');
			if (!empty($this->input->post('user_is_gold'))) $user['user_is_gold'] = $this->input->post('user_is_gold');

            if (!empty($this->input->post('user_password')) && $this->input->post('user_password')!='oldpassword'){
                $user['user_password'] = sha1($this->input->post('user_password'));
            }

            $this->user_model->updateRecord($user, 'user_id');

			$new_grade = $user['user_grade'];
			if ($old_grade < $new_grade) {
				$cnt = $this->input->post('stamp_count');

		        $this->load->model('rank_prefer_model');
				$this->load->model('user_stamp_care_model');
				$this->load->model('user_coupon_model');


				$prefers = $this->rank_prefer_model->getChangeGradePrefers($user['company_id'], $old_grade, $new_grade, $old_stamp_cnt, $cnt);

				foreach($prefers as $prefer){
							
					$old_care = $this->user_stamp_care_model->getDataByParam(['user_id' => $user_id, 'rank_prefer_id' => $prefer['rank_prefer_id']]);
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
								'rank_prefer_id' => $prefer['rank_prefer_id'],
								'visible' => 1,
							]);
					}
				}
				
			}
        }

//        $s = '[{"id":"","user_id":"null","ticket_id":"1","count":"0","is_reset":"0","reset_time_type":"0","reset_time_value":"0","reset_count":"0"},{"id":"","user_id":"null","ticket_id":"6","count":"0","is_reset":"0","reset_time_type":"0","reset_time_value":"0","reset_count":"0"}]';
        if (!empty($this->input->post('user_tickets'))) {
            $user_tickets = json_decode($this->input->post('user_tickets'), true);
//        $user_tickets = json_decode($s, true);
            foreach ($user_tickets as $user_ticket) {
                if ($user_ticket['max_count'] > 0 && intval($user_ticket['count']) > intval($user_ticket['max_count'])) 
                    $user_ticket['count'] = $user_ticket['max_count'];
                if (empty($user_ticket['id'])) {
                    $ticket = array(
                        'user_id' => $user_ticket['user_id'],
                        'ticket_id' => $user_ticket['ticket_id'],
                        'count' => $user_ticket['count'],
                        'is_reset' => $user_ticket['is_reset'],
                        'reset_time_type' => $user_ticket['reset_time_type'],
                        'reset_time_value' => $user_ticket['reset_time_value'],
                        'reset_count' => $user_ticket['reset_count'],
                        'max_count' => $user_ticket['max_count'],
                    );

                    $this->user_ticket_model->insertRecord($ticket);
                } else {
                    $ticket = $this->user_ticket_model->getFromId($user_ticket['id']);
                    $ticket['user_id'] = $user_ticket['user_id'];
                    $ticket['ticket_id'] = $user_ticket['ticket_id'];
                    $ticket['count'] = $user_ticket['count'];
                    $ticket['is_reset'] = $user_ticket['is_reset'];
                    $ticket['reset_time_type'] = $user_ticket['reset_time_type'];
                    $ticket['reset_time_value'] = $user_ticket['reset_time_value'];
                    $ticket['reset_count'] = $user_ticket['reset_count'];
                    $ticket['max_count'] = $user_ticket['max_count'];
                    $this->user_ticket_model->updateRecord($ticket, 'id');
                }
            }
        }
//        if (!empty($user_id) && (!empty($this->input->post('is_reset_ticket')) || $this->input->post('is_reset_ticket')=='0' )){
//            $reset_setting = $this->user_ticket_reset_setting_model->getResetSetting(['user_id'=>$user_id]);
//            if (empty($reset_setting)){
//                $data = array(
//                    'user_id'=>$user_id,
//                    'is_enable' =>$this->input->post('is_reset_ticket'),
//                    'time_type'=>$this->input->post('ticket_reset_type'),
//                    'time_value'=>$this->input->post('ticket_reset_day'),
//                    'ticket_value'=>$this->input->post('ticket_reset_value'),
//                );
//                $this->user_ticket_reset_setting_model->insertRecord($data);
//            }else{
//
//                $reset_setting['is_enable'] = $this->input->post('is_reset_ticket');
//                $reset_setting['time_type'] = $this->input->post('ticket_reset_type');
//                $reset_setting['time_value'] = $this->input->post('ticket_reset_day');
//                $reset_setting['ticket_value'] = $this->input->post('ticket_reset_value');
//                $this->user_ticket_reset_setting_model->updateRecord($reset_setting, 'id');
//            }
//
//        }

		if (!empty($this->input->post('stamp_count'))) {
			$cnt = $this->input->post('stamp_count');
			

			$cond = [];
			$cond['user_id'] = $user_id;
			$cond['use_flag'] = 0;

			$stamps = $this->stamp_model->getStampList($cond);
			$old_cnt = count($stamps);

			if ($cnt>$old_cnt) {
				for ($i=0; $i<$cnt-$old_cnt ; $i++ ) {
					$this->stamp_model->insertRecord([
							'date' => date('Y-m-d'),
							'user_id' => $user_id,
							'company_id' => $company_id,
							'organ_id' => null,
							'use_flag' => 0,
							'stamp_count' => 1,
						]);
				}
			}
			if ($cnt<$old_cnt) {
				for ($i=0; $i<$old_cnt-$cnt ; $i++ ) {
					$del_stamp = $stamps[$old_cnt-$i-1];
					$this->stamp_model->delete_force($del_stamp['stamp_id'], 'stamp_id');
				}
			}


		}

        $results['isSave'] = true;
        $results['user_id'] = $user_id;
        $results['user_name'] = empty($user['user_nick']) ? ($user['user_first_name'].' '.$user['user_last_name']) : $user['user_nick'];

        echo json_encode($results);
    }

    public function getRegisterUserInfo(){
        $company_id = $this->input->post('company_id');
        $device_token = $this->input->post('device_token');
        if (empty($device_token)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $user = $this->user_model->getUserByToken($device_token, $company_id);

        if (empty($user)){
            $results['isLoad'] = false;
        }else{
            $results['isLoad'] = true;
            $results['user'] = $user;
        }

        echo json_encode($results);
    }

    public function generateUserCode(){
        $user_code = 0;
        while($user_code==0){
            $tmpUserCode = rand(10000000000, 99999999999);
            $exit_code_user = $this->user_model->getRecordByCond(['user_no'=>$tmpUserCode]);
            if (empty($exit_code_user)){
                $user_code = $tmpUserCode;
            }
        }

        return $user_code;
    }
    public function generateUserQRCode($user_no, $company_id){
        $domain = '';
        if ($company_id==1) $domain = 'conceptbar.info';
        if ($company_id==2) $domain = 'riraku-kan.jp';
        if ($company_id==3) $domain = 'koritori.jp';
        if ($company_id==4) $domain = 'libero-school.com';

        $company_code = substr('000'.$company_id, strlen('000'.$company_id)-3);

        $sum_check = 0;
        foreach (str_split($user_no) as $each ){
            $sum_check = $sum_check + $each;
        }

        $qr_code = 'connect!'.$user_no.'!'.$domain.'!'.$company_code.'!'.$sum_check;

        return $qr_code;
    }

    public function deleteUser(){
        $user_id = $this->input->post('user_id');
        if (empty($user_id)){
            echo json_encode(['isDelete'=>false]);
            return;
        }
        $this->user_model->delete_force($user_id, 'user_id');

        $user_tickets = $this->user_ticket_model->getListByCond(['user_id'=>$user_id]);
        foreach ($user_tickets as $user_ticket){
            $this->user_ticket_model->delete_force($user_ticket['id'], 'id');
        }

        echo json_encode(['isDelete'=>true]);
    }

    public function loginCheck(){
        $user_id = $this->input->post('user_id');
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $is_login = false;
        if (!empty($user_id)){
            $user = $this->user_model->getFromId($user_id);
            if (!empty($user)){
                if ($user['user_email']==$email && sha1($password) == $user['user_password']){
                    $is_login = true;
                }
            }
        }

        $results['isLogin'] = $is_login;

        echo json_encode($results);
    }
    
    public function getOwnerTickets(){
        $user_id = $this->input->post('user_id');

        $tickets = $this->user_ticket_model->getListByCond(['user_id'=>$user_id]);

        $results['isLoad'] = true;
        $results['tickets'] = $tickets;

        echo json_encode($results);
    }

    public function usingTicketWithCheckIn(){
        $user_ticket_id = $this->input->post('id');
        $use_count = $this->input->post('use_count');
        $user_ticket = $this->user_ticket_model->getFromId($user_ticket_id);

        $user_ticket['count'] = $user_ticket['count'] - $use_count;
        if ($user_ticket['count']<0) $user_ticket['count'] = 0;
        $this->user_ticket_model->updateRecord($user_ticket, 'id');

        $results['isUsing'] = true;

        echo json_encode($results);
    }

    public function updateUserPush(){
        $user_id = $this->input->post('user_id');
        $push_key = $this->input->post('push_key');
        $push_value = $this->input->post('push_value');

        $user = $this->user_model->getFromId($user_id);
        if ($push_key=='message') $user['is_message_push'] = $push_value;
        if ($push_key=='reserve_request') $user['is_reserve_request_push'] = $push_value;
        if ($push_key=='reserve_apply') $user['is_reserve_apply_push'] = $push_value;

        $this->user_model->updateRecord($user, 'user_id');

        $result['isUpdate'] = true;
        echo json_encode($result);

    }
    public function updateDeviceToken(){
        $user_id = $this->input->post('user_id');
        $device_token = $this->input->post('device_token');

        $user = $this->user_model->getFromId($user_id);
        $user['user_device_token'] = $device_token;

        $this->user_model->updateRecord($user, 'user_id');

        $result['isUpdate'] = true;
        echo json_encode($result);

    }

    public function sendResetEmail(){
        $company_id = $this->input->post('company_id');
        $user_email = $this->input->post('email');
        $cond['user_email'] = $user_email;
        $cond['company_id'] = $company_id;
        $user = $this->user_model->getOneByParam($cond);

        if (empty($user)){
            $results['isLoad'] = false;
        }else{
            $company = $this->company_model->getFromId($user['company_id']);
            try {
               
                $password = rand(10000, 99999);
                $user['user_password'] = sha1($password);
                $this->user_model->updateRecord($user, 'user_id');

				$title = $company['company_name'].'アプリのパスワードが初期化されました。';
                $body = "「".$user['user_first_name']." ".$user['user_last_name']."」様

VISITの自動送信メールです


パスワードのリセットが完了しました。
新しいパスワードは　「".$password."」　です。

よろしくお願い致します。";

				$this->sendMailMessage($title, $body, $user_email);

                $results['isLoad'] = true;
            }catch (Exception $e){
                return false;
            }
        }

        echo json_encode($results);
    }

    public function registerVerifyCode(){
        $company_id = $this->input->post('company_id');
        $user_email = $this->input->post('email');
        $cond['email'] = $user_email;
        $cond['company_id'] = $company_id;

        $this->load->model('verify_code_model');

        $data = $this->verify_code_model->getOneByParam($cond);
        $code = rand(1000, 9999);

        if (empty($data)){
            $data = array(
                'company_id' => $company_id,
                'email' => $user_email,
                'code' => $code
            );

            $this->verify_code_model->insertRecord($data);
        }else{
            $data['code'] = $code;
            $this->verify_code_model->updateRecord($data);
        }
        try {
			
			$company_name = 'VISITの自動送信メール';
			if ($company_id == '1') $company_name = 'Shangri-La group アプリ';
			if ($company_id == '2') $company_name = 'Shangri-La group アプリ';
			if ($company_id == '3') $company_name = 'Shangri-La group アプリ';
			if ($company_id == '4') $company_name = 'Shangri-La group アプリ';


			$title = 'VISITの自動送信メール';
            $body = $company_name . "

セキュリティコードは
「".$code."」　
です。";

			$this->sendMailMessage($title, $body, $user_email);


        }catch (Exception $e){
            return false;
        }
        $results['isLoad'] = true;

        echo json_encode($results);
    }

    public function userVerifyAndRegister(){
        $company_id = $this->input->post('company_id');
        $user_email = $this->input->post('user_email');
        $code = $this->input->post('code');

        $cond['email'] = $user_email;
        $cond['company_id'] = $company_id;
        $cond['code'] = $code;
        $this->load->model('verify_code_model');
        $data = $this->verify_code_model->getOneByParam($cond);
        if (empty($data)){
            $results['isVerify'] = false;
            echo json_encode($results);
            return;
        }

        $user_code = $this->generateUserCode();

        $user = array(
            'company_id' => $company_id,
            'user_no' => $user_code,
            'user_grade' => '1',
            'user_qrcode' => $this->generateUserQRCode($user_code, $company_id),
            'user_first_name' => empty($this->input->post('user_first_name')) ? null : $this->input->post('user_first_name'),
            'user_last_name' => empty($this->input->post('user_last_name')) ? null : $this->input->post('user_last_name'),
            'user_nick' => $this->input->post('user_nick'),
            'user_email' => empty($this->input->post('user_email')) ? null : $this->input->post('user_email'),
            'user_tel' => empty($this->input->post('user_tel')) ? null : $this->input->post('user_tel'),
            'user_sex' => empty($this->input->post('user_sex')) ? null : $this->input->post('user_sex'),
            'user_birthday' => empty($this->input->post('user_birthday')) ? null : $this->input->post('user_birthday'),
            'user_ticket' => 0,
            'user_device_token' => empty($this->input->post('user_device_token')) ? '' : $this->input->post('user_device_token'),
            'user_password' => empty($this->input->post('user_password')) ? null : sha1($this->input->post('user_password')),
            'visible' => 1,
        );
        $user_id = $this->user_model->insertRecord($user);

        $results['isVerify'] = true;
        $results['user_id'] = $user_id;
        $results['user_name'] = $this->input->post('user_first_name').' '. $this->input->post('user_last_name');
        $results['user_nick'] = $this->input->post('user_nick');

        echo json_encode($results);


    }


    public function userNoVerifyRegister(){
        $company_id = $this->input->post('company_id');
        $user_email = $this->input->post('user_email');

        // $cond['email'] = $user_email;
        // $cond['company_id'] = $company_id;

        // $this->load->model('verify_code_model');
        // $data = $this->verify_code_model->getOneByParam($cond);
        // if (empty($data)){
        //     $results['isVerify'] = false;
        //     echo json_encode($results);
        //     return;
        // }

        $user_code = $this->generateUserCode();

        $user = array(
            'company_id' => $company_id,
            'user_no' => $user_code,
            'user_grade' => '1',
            'user_qrcode' => $this->generateUserQRCode($user_code, $company_id),
            'user_first_name' => empty($this->input->post('user_first_name')) ? null : $this->input->post('user_first_name'),
            'user_last_name' => empty($this->input->post('user_last_name')) ? null : $this->input->post('user_last_name'),
            'user_nick' => $this->input->post('user_nick'),
            'user_email' => empty($this->input->post('user_email')) ? null : $this->input->post('user_email'),
            'user_tel' => empty($this->input->post('user_tel')) ? null : $this->input->post('user_tel'),
            'user_sex' => empty($this->input->post('user_sex')) ? null : $this->input->post('user_sex'),
            'user_birthday' => empty($this->input->post('user_birthday')) ? null : $this->input->post('user_birthday'),
            'user_ticket' => 0,
            'user_device_token' => empty($this->input->post('user_device_token')) ? '' : $this->input->post('user_device_token'),
            'user_password' => empty($this->input->post('user_password')) ? null : sha1($this->input->post('user_password')),
            'visible' => 1,
        );
        $user_id = $this->user_model->insertRecord($user);

        $results['isVerify'] = true;
        $results['user_id'] = $user_id;
        $results['user_name'] = $this->input->post('user_first_name').' '. $this->input->post('user_last_name');
        $results['user_nick'] = $this->input->post('user_nick');

        echo json_encode($results);


    }

    public function updateUserProfile(){
        $user_id = $this->input->post('user_id');

        $user = $this->user_model->getFromId($user_id);

        if (empty($user['user_no'])){
            $user['user_no'] = $this->generateUserCode();
            $user['user_qrcode'] = $this->generateUserQRCode($user['user_no'], $user['company_id']);
        }

        if (!empty($this->input->post('user_first_name'))) $user['user_first_name'] = $this->input->post('user_first_name');
        if (!empty($this->input->post('user_last_name'))) $user['user_last_name'] = $this->input->post('user_last_name');
        if (!empty($this->input->post('user_nick'))) $user['user_nick'] = $this->input->post('user_nick');
        if (!empty($this->input->post('user_tel'))) $user['user_tel'] = $this->input->post('user_tel');
        if (!empty($this->input->post('user_sex'))) $user['user_sex'] = $this->input->post('user_sex');
        //if (!empty($this->input->post('user_birthday'))) $user['user_birthday'] = $this->input->post('user_birthday');
        if (!empty($this->input->post('user_ticket'))) $user['user_ticket'] = $this->input->post('user_ticket');
        if (!empty($this->input->post('user_password')) && $this->input->post('user_password')!='oldpassword'){
            $user['user_password'] = sha1($this->input->post('user_password'));
        }

		$birth = $this->input->post('user_birthday');
		if(!empty($birth) && $birth <> $user['user_birthday']){
			$user['user_birthday'] = $birth;
			$user['is_change_birthday'] = 1;
		}
		


        $this->user_model->updateRecord($user, 'user_id');

        $results['isUpdate'] = $user;
        echo json_encode($results);


    }



}
?>