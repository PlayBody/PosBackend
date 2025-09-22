<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apiadvises extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('advise_model');
        $this->load->model('teacher_model');
        $this->load->model('staff_model');
    }


    public function loadAdviseList(){
        $company_id = $this->input->post('company_id');
        $user_id = $this->input->post('user_id');
        $cond = [];
        if (!empty($user_id)){
            $cond['user_id'] = $user_id;
        }

        if (!empty($company_id)){
            $cond['company_id'] = $company_id;
        }

        $advise_list = $this->advise_model->getListByCond($cond);

        $results['isLoad'] = true;
        $results['advise_list'] = $advise_list;

        echo json_encode($results);
    }

    public function loadAdviseInfo(){
        $advise_id = $this->input->post('advise_id');

        $advise = $this->advise_model->getFromId($advise_id);

        $staff = $this->staff_model->getFromId($advise['teacher_id']);

        $advise['teacher_name'] = ($staff['staff_first_name']==null?'':$staff['staff_first_name']) . ' ' . ($staff['staff_last_name']==null?'':$staff['staff_last_name']);

        $results['isLoad'] = true;
        $results['advise'] = $advise;

        echo json_encode($results);
    }

    public function saveAdviseInfo(){
        $advise_id = $this->input->post('advise_id');
        $user_id = $this->input->post('user_id');
        $teacher_id = $this->input->post('teacher_id');
        $question = $this->input->post('question');
        $answer = $this->input->post('answer');
        $movie_file = $this->input->post('movie');
        $answer_movie_file = $this->input->post('answer_movie_file');

        if (empty($advise_id)){
            $advise = array(
                'user_id'=>$user_id,
                'teacher_id'=>$teacher_id,
                'question' => $question,
                'movie_file'=>$movie_file,
                'answer_movie_file'=>empty($answer_movie_file)? null : $answer_movie_file,
                'visible'=>'1'
            );

            $this->advise_model->insertRecord($advise);

        }else{
            $advise = $this->advise_model->getFromId($advise_id);
            $advise['answer'] = $answer;
            $advise['answer_movie_file'] = empty($answer_movie_file)? null : $answer_movie_file;

            $this->advise_model->updateRecord($advise, 'advise_id');
        }

        $results['isSave'] = true;
        $results['advise_id'] = true;

        echo json_encode($results);
    }


    function uploadVideo() {

        $result = array();

        // user photo
        $upload_path = "assets/video/advise/";
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



}
?>