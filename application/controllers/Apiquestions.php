<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apiquestions extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('question_favorite_model');
        $this->load->model('question_model');
    }


    public function loadQuestions(){
        $company_id = $this->input->post('company_id');

        $questions = $this->question_model->getListQuestionData(['company_id'=>$company_id]);

        $results['isLoad'] = true;
        $results['questions'] = $questions;

        echo json_encode($results);
    }

    public function saveQuestion(){
        $user_id = $this->input->post('user_id');
        $title = $this->input->post('title');
        $question = $this->input->post('question');
        $question_id = $this->input->post('question_id');

        if (empty($question_id)){
            $question_data = array(
                'user_id'=>$user_id,
                'question_title'=>$title,
                'question' => $question,
                'visible' => 1
            );

            $this->question_model->insertRecord($question_data);
        }

        $results['isSave'] = true;

        echo json_encode($results);

    }

    public function loadFavoriteQuestions(){
        $company_id = $this->input->post('company_id');

        $questions = $this->question_favorite_model->getListQuestionData($company_id);

        $results['isLoad'] = true;
        $results['questions'] = $questions;

        echo json_encode($results);
    }

    public function saveFavoriteQuestion(){
        $company_id = $this->input->post('company_id');
        $question = $this->input->post('question');
        $answer = $this->input->post('answer');

        $question = array(
                'company_id' => $company_id,
                'question' => $question,
                'answer' => $answer,
                'visible' => 1,
            );
            $question_id = $this->question_favorite_model->insertRecord($question);


        $results['isSave'] = true;

        echo json_encode($results);
    }

}
?>