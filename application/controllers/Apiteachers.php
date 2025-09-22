<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apiteachers extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('teacher_model');
    }


    public function loadTeacherList(){
        $company_id = $this->input->post('company_id');

        $list = $this->teacher_model->getListByCond(['company_id'=>$company_id]);

        $results['isLoad'] = true;
        $results['teachers'] = $list;

        echo json_encode($results);
    }

    public function saveTeacher(){
        $company_id = $this->input->post('company_id');
        $teacher_id = $this->input->post('teacher_id');
        $teacher_name = $this->input->post('teacher_name');

        if (empty($teacher_id)){
            $teacher = array(
                'company_id' => $company_id,
                'teacher_name' => $teacher_name,
                'visible' => 1,
            );
            $user_id = $this->teacher_model->insertRecord($teacher);
        }else{
            $teacher = $this->teacher_model->getFromId($teacher_id);

            $teacher['teacher_name'] = $teacher_name;

            $this->teacher_model->updateRecord($teacher, 'teacher_id');
        }

        $results['isSave'] = true;
        //$results['teacher_id'] = $teacher_id;

        echo json_encode($results);
    }

}
?>