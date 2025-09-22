<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apimessages extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {

        parent::__construct();

        $this->load->model('message_model');
        $this->load->model('user_model');
        $this->load->model('device_token_model');
        $this->load->model('fitness_model');
        $this->load->model('group_user_model');
        $this->load->model('staff_model');
        $this->load->model('staff_organ_model');
    }

    public function loadMessageUserList(){
        $company_id = $this->input->post('company_id');
        $search = $this->input->post('search');

        if (empty($company_id) ){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $messageUsers = $this->message_model->getMessageUserLists($company_id, $search);

        $results['isLoad'] = true;
        $results['message_users'] = $messageUsers;

        echo json_encode($results);
    }

    public function loadMessages(){
        $company_id = $this->input->post('company_id');
        $user_id = $this->input->post('user_id');
        $user_type = $this->input->post('user_type');
        $is_group = $this->input->post('is_group');

        if (empty($company_id) ||  (empty($user_id) && $user_id!='0' )){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $cond = [];
        $cond['company_id'] = $company_id;
        if ($is_group == '1' ){
            $cond['group_id'] = $user_id;
        }else{
            $cond['group_id'] = '';
            $cond['user_id'] = $user_id;
        }

        $messages = $this->message_model->getMessageList($cond);

        foreach ($messages as $message){
            if ($message['type']==$user_type) {
                $message['read_flag'] = 1;
                $this->message_model->updateRecord($message, 'message_id');
            }
        }

        $results['isLoad'] = true;
        $results['messages'] = $messages;

        echo json_encode($results);

    }

    public function sendMessage()
    {
        $company_id = $this->input->post('company_id');
        $user_id = $this->input->post('user_id');
        $staff_id = $this->input->post('staff_id');
        $content = $this->input->post('content');
        $type = $this->input->post('type');
        $file_type = $this->input->post('file_type');
        $file_url = $this->input->post('file_url');
        $file_name = $this->input->post('file_name');
        $video_url = $this->input->post('video_url');
        $is_group = $this->input->post('is_group');

        if (empty($company_id) || (empty($user_id) && $user_id!='0' )){
            $results['isSend'] = false;
            echo json_encode($results);
            return;
        }

        $group_key = null;
        if ($is_group=='1'){
            $group_key = $company_id . '-' . $user_id. '-' . date('YmdHis') . '-' . md5(uniqid(rand(), true));

            $group_id = $user_id;
            if ($group_id=='0'){
                $users = $this->user_model->getUsersByCond(['company_id'=>$company_id]);
            }else{
                $users = $this->group_user_model->getUsersByGroupGroup($group_id);
            }
        }else{
            $group_id = null;

            $users[]['user_id'] = $user_id;
        }

        $title = '';
        if ($type=='2'){
            $staff = $this->staff_model->getFromId($staff_id);
            $title = ($staff['staff_nick'] == null ? ($staff['staff_first_name'] . ' ' . $staff['staff_last_name']) : $staff['staff_nick']) . '様からメッセージが届きました。';
        }

        $is_fcm = false;
        foreach ($users as $user){
            $message = array(
                'company_id' => $company_id,
                'user_id' => $user['user_id'],
                'content' => $content,
                'file_type' => empty($file_type) ? null : $file_type,
                'file_url' => empty($file_url) ? null : $file_url,
                'file_name' => empty($file_name) ? null : $file_name,
                'video_url' => empty($video_url) ? null : $video_url,
                'type' => $type,
                'group_id' => $group_id,
                'group_key' =>$group_key,
            );

            $this->message_model->insertRecord($message);

            $this->load->model('notification_text_model');
            if ($type=='1'){
                $user = $this->user_model->getFromId($user['user_id']);

                $text_data = $this->notification_text_model->getRecordByCond(['company_id'=>$company_id, 'mail_type'=>'16']);
                $title = empty($text_data['title']) ? 'タイトルなし' : $text_data['content'];
                $content = empty($text_data['content']) ? 'タイトルなし' : $text_data['content'];
                $content = str_replace('$user_name', $user['user_first_name'] . ' ' . $user['user_last_name'], $content);

                $receive_staffs = $this->staff_model->getStaffList(['company_id'=>$company_id, 'staff_auth'=>'2']);
                foreach ($receive_staffs as $receive_staff){
                    $is_fcm = $this->sendNotifications('16', $title, $content, $user['user_id'], $receive_staff['staff_id'], '1');
                }

            }else{
                $is_fcm = $this->sendNotifications('message', $title, $content, $company_id, $user['user_id'], '2');
            }

        }


        $results['isSend'] = true;
        $results['isSendFcm'] = $is_fcm;

        echo json_encode($results);

    }

    public function loadFitnesses(){
        $company_id = $this->input->post('company_id');
        $group_id = $this->input->post('group_id');

        if (empty($company_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $cond = [];
        $cond['company_id'] = $company_id;
        if (!empty($group_id)){
            $cond['group_id'] = $group_id;
        }
        $list = $this->fitness_model->getListByCond($cond);

        $results['isLoad'] = true;
        $results['messages'] = $list;

        echo json_encode($results);

    }

    public function saveFitness(){
        $company_id = $this->input->post('company_id');
        $group_id = $this->input->post('group_id');
        $message = $this->input->post('message');
        $file_type = $this->input->post('file_type');
        $file_url = $this->input->post('file_url');

        if (empty($company_id)){
            $results['isSave'] = false;
            echo json_encode($results);
            return;
        }

        $fitness = array(
            'company_id'=>$company_id,
            'group_id'=>empty($group_id) ? null : $group_id,
            'message'=>$message,
            'file_type' => empty($file_type) ? null : $file_type,
            'file_url' => empty($file_url) ? null : $file_url,
        );

        $this->fitness_model->insertRecord($fitness);

        $results['isSave'] = true;

        echo json_encode($results);

    }

    public function getStaffUnreadCount(){
        $company_id = $this->input->post('company_id');
        $count = $this->message_model->getUnreadMessageCount('1', $company_id);

        $results['count'] = empty($count)?0 : $count;
        echo json_encode($results);

    }
    public function getUserUnreadCount(){
        $user_id = $this->input->post('user_id');
        $count = $this->message_model->getUnreadMessageCount('2', $user_id);

        $results['count'] = empty($count)?0 : $count;
        echo json_encode($results);

    }


    function uploadAttachment() {

        $result = array();

        // user photo
        $upload_path = "assets/messages/";
        if(!is_dir($upload_path)) {
            mkdir($upload_path);
        }
        $path  = base_url().$upload_path;
        $fileName = $_FILES['upload']['name'];
        $config = array(
            'upload_path'   => $upload_path,
            'allowed_types' => '*',
            'overwrite'     => 1,
            'file_name' 	=> $fileName
        );
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!empty($fileName)) {
            if ($this->upload->do_upload('upload')) {
                $file_url = $path.$this->upload->file_name;
                //		$data = array('username' => $username, 'picture' => $file_url, 'about_me' => $aboutme,'user_location' => $userlocation, 'user_birthday' => $userbirthday, 'latitude' => $latitude, 'longitude' => $longitude);
                //		$this->api_model->update_query("tb_user", $condition, $data);
                $result['isUpload'] = true;
            } else {
                $result['isUpload'] = false;
            }
        }else{
            $result['isUpload'] = false;
        }

        echo json_encode($result);

    }

    public function loadStaffMessages(){
        $user_id = $this->input->post('user_id');
        $company_id = $this->input->post('company_id');
        $auth = $this->input->post('auth');
        $staff_id = $this->input->post('staff_id');
        $is_group = $this->input->post('is_group');

        $cond = [];
        if($auth<4){
            $organs = $this->staff_organ_model->getStaffOrganList(['staff_id'=>$staff_id]);
            $cond['organs'] = join(',', array_column($organs,'organ_id'));
        }
        $cond['company_id'] = $company_id;
        $cond['user_id'] = $user_id;

        if(!empty($is_group)){
            $cond['user_id'] = '';
            $cond['group_id'] = $user_id;
        }

        $messages = $this->message_model->getMessageList($cond);

        foreach ($messages as $message){
            if ($message['type']==1) {
                $updateMessage = $this->message_model->getFromId($message['message_id']);
                $updateMessage['read_flag'] = 1;
                $this->message_model->updateRecord($updateMessage, 'message_id');
            }
        }

        $results['isLoad'] = true;
        $results['messages'] = $messages;

        echo json_encode($results);

    }

    public function sendStaffMessage()
    {
        $user_id = $this->input->get_post('user_id');
        $staff_id = $this->input->get_post('staff_id');
        $content = $this->input->get_post('content');
        $file_type = $this->input->get_post('file_type');
        $file_url = $this->input->get_post('file_url');
        $file_name = $this->input->get_post('file_name');
        $video_url = $this->input->get_post('video_url');
        $is_group = $this->input->get_post('is_group');
        $company_id = $this->input->get_post('company_id');

        $staff = $this->staff_model->getFromId($staff_id);
        $title = ($staff['staff_nick'] == null ? ($staff['staff_first_name'] . ' ' . $staff['staff_last_name']) : $staff['staff_nick']) . '様からメッセージが届きました。';

        $group_id = null;
        $group_key = null;
        if ($is_group=='1'){
            $group_key = $company_id . '-' . $user_id. '-' . date('YmdHis') . '-' . md5(uniqid(rand(), true));

            $group_id = $user_id;
            if ($group_id=='00'){
                $users = $this->user_model->getUsersByCond(['company_id'=>$company_id]);
            }else{
                $users = $this->group_user_model->getUsersByGroupGroup($group_id);
            }
        }else{
            $users[]['user_id'] = $user_id;
        }
        $is_fcm = true;
        foreach ($users as $user) {
            $message = array(
                'company_id' => $company_id,
                'user_id' => $user['user_id'],
                'content' => $content,
                'staff_id' => $staff_id,
                'file_type' => empty($file_type) ? null : $file_type,
                'file_url' => empty($file_url) ? null : $file_url,
                'file_name' => empty($file_name) ? null : $file_name,
                'video_url' => empty($video_url) ? null : $video_url,
                'type' => 2,
                'group_id' => $group_id,
                'group_key' => $group_key,
            );

            $this->message_model->insertRecord($message);

            $this->load->model('notification_text_model');
            $is_send_notification = $this->sendNotifications('message', $title, $content, $company_id, $user['user_id'], '2');
            if(!$is_send_notification) $is_fcm = $is_send_notification;
        }
        $results['isSend'] = true;
        $results['isSendFcm'] = $is_fcm;

        echo json_encode($results);

    }

    public function loadUserMessages(){
        $company_id = $this->input->post('company_id');
        $user_id = $this->input->post('user_id');

        if (empty($company_id) || empty($user_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $cond = [];
        $cond['company_id'] = $company_id;
        $cond['user_id'] = $user_id;

        $messages = $this->message_model->getMessageList($cond);

        foreach ($messages as $message){
            if ($message['type'] == '2') {
                $updateMessage = $this->message_model->getFromId($message['message_id']);
                $updateMessage['read_flag'] = 1;
                $this->message_model->updateRecord($updateMessage, 'message_id');
            }
        }

        $results['isLoad'] = true;
        $results['messages'] = $messages;

        echo json_encode($results);

    }

    public function sendUserMessage(){
        $organ_id = $this->input->post('organ_id');
        $company_id = $this->input->post('company_id');
        $user_id = $this->input->post('user_id');
        $content = $this->input->post('content');
        $file_type = $this->input->post('file_type');
        $file_url = $this->input->post('file_url');
        $file_name = $this->input->post('file_name');
        $video_url = $this->input->post('video_url');

        if (empty($company_id)){
            $results['isSend'] = false;
            echo json_encode($results);
            return;
        }

        $message = array(
            'company_id' => $company_id,
            'user_id' => $user_id,
            'organ_id' => empty($organ_id)?null : $organ_id,
            'content' => $content,
            'type' => '1',
            'file_type' => empty($file_type) ? null : $file_type,
            'file_url' => empty($file_url) ? null : $file_url,
            'file_name' => empty($file_name) ? null : $file_name,
            'video_url' => empty($video_url) ? null : $video_url,
        );

        $this->message_model->insertRecord($message);

        $this->load->model('notification_text_model');

        $user = $this->user_model->getFromId($user_id);

        $text_data = $this->notification_text_model->getRecordByCond(['company_id'=>$company_id, 'mail_type'=>'16']);
        $title = empty($text_data['title']) ? 'タイトルなし' : $text_data['content'];
        $content = empty($text_data['content']) ? '' : $text_data['content'];
        $content = str_replace('$user_name', $user['user_first_name'] . ' ' . $user['user_last_name'], $content);

        $cond1['company_id'] = $company_id;
        $cond1['min_auth'] = 4;
        $cond1['max_auth'] = 4;
        $receive_staffs = $this->staff_model->getStaffs($cond1);

        if (!empty($organ_id)){
            $cond2['company_id'] = $company_id;
            $cond2['organ_id'] = $organ_id;
            $cond2['min_auth'] = 2;
            $cond2['max_auth'] = 3;
            $receive_bosses = $this->staff_model->getStaffs($cond1);
            $receive_staffs = array_merge($receive_staffs, $receive_bosses);
        }

        $is_fcm = true;
        foreach ($receive_staffs as $receive_staff){
            $isSendNotification = $this->sendNotifications('16', $title, $content, $user['user_id'], $receive_staff['staff_id'], '1');
            if(!$isSendNotification) $is_fcm = $isSendNotification;
        }

        $results['isSend'] = true;
        $results['isSendFcm'] = $is_fcm;

        echo json_encode($results);
    }


}
?>