<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';
/**
 * Class : Scenario
 *
 */
class Scenario extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_COMPANY);
        $this->load->model('scenario_model');

        //チャットボット
        $this->load->model('scenario_model');
        $this->load->model('bot_model');

        $this->header['page'] = 'scenario';
        $this->header['title'] = '企業管理画面｜シナリオ一覧';

    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {

        $this->data['list'] = $this->scenario_model->getScenarioList($this->user['company_id']);

        $this->_load_view("scenario/index");
    }

    public function get()
    {
        $_id = $this->input->get_post('id');
        $scenario = $this->scenario_model->getDetail($_id,$this->user['company_id']);
        $this->output->set_content_type('application/json')->set_output(json_encode($scenario));
    }
    /**
     * This function is used to load the add new form
     */
    function add()
    {
        $parent_id = $this->input->post('parent_id',0);
        $level = $this->input->post('level',1);
        $edit_val = $this->input->post('edit_val','');
        $scenario_title = $this->input->post('scenario_title');
        $scenario_content = $this->input->post('scenario_content');

        if (empty($level)) {
            $level = 1;
        }else{
            $level = $level + 1;
        }

        if (empty($parent_id)) $parent_id = 0;

        if ($edit_val == "edit") {
            $data = array(
                'scenario_id'=>$parent_id,
                'title'=>$scenario_title,
                'content'=>$scenario_content,
            );
            $this->scenario_model->edit($data, 'scenario_id');
        }else{
            $groupMaxOrder = $this->scenario_model->getGroupMaxID($parent_id);
            if (empty($groupMaxOrder)) {
                $groupMaxOrder = 1;
            }else{
                $groupMaxOrder = $groupMaxOrder["group_order"] + 1;
            }

            $data = array(
                'company_id'=>$this->user['company_id'],
                'parent_id'=>$parent_id,
                'level'=>$level,
                'title'=>$scenario_title,
                'content'=>$scenario_content,
                'group_order'=>$groupMaxOrder,
            );
            $insert_id = $this->scenario_model->add($data);

            $code = "";
            if ($parent_id == 0) {
                $code = sprintf("%'.04d", $groupMaxOrder);
            }else{
                $parentData = $this->scenario_model->getOneByParam(array("scenario_id"=>$parent_id));
                $parent_tree_code = $parentData["tree_code"];
                $code = $parent_tree_code . sprintf("%'.04d", $groupMaxOrder);
                $this->scenario_model->edit(array("child_flag"=>1, "scenario_id"=>$parent_id), "scenario_id");
            }
            $this->scenario_model->edit(array("tree_code"=>$code, "scenario_id"=>$insert_id), "scenario_id");
        }

        redirect("scenario/");
    }

    function delete($id=0)
    {
        $result = false;
        if($id==0){
            return $this->output->set_output(json_encode( array('ok'=>false,'result'=>$result) ));
        }
        $root = $this->scenario_model->get($id);
        if(empty($root)){
            return $this->output->set_output(json_encode( array('ok'=>false,'result'=>$result) ));
        }
        $child_count = $this->scenario_model->get_tree($root['tree_code'],true);

        if($child_count){

            $this->scenario_model->delete_tree($root['tree_code']);
            $result = $child_count;
            $this->scenario_model->edit(array('scenario_id'=>$root['parent_id'],'child_flag'=>0),'scenario_id');
            return $this->output->set_output(json_encode( array('ok'=>true,'result'=>$result) ));
        }
        return $this->output->set_output(json_encode( array('ok'=>false,'result'=>$result) ));

    }
}
