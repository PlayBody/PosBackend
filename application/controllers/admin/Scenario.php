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
        parent::__construct(ROLE_ADMIN);
        $this->load->model('scenario_model');
        $this->header['page'] = 'scenario';
        $this->header['title'] = 'シナリオ';
        $this->header['user'] = $this->user;
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->data['list'] = $this->scenario_model->getScenarioList();

        $this->_load_view_admin("admin/scenario/index");
    }

    public function get()
    {
        $_id = $this->input->get_post('id');
        $scenario = $this->scenario_model->getDetail($_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($scenario));
    }
    /**
     * This function is used to load the add new form
     */
    function add()
    {
        $parent_id = $this->input->post('parent_id');
        $level = $this->input->post('level');
        $edit_val = $this->input->post('edit_val');
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
                'id'=>$parent_id,
                'title'=>$scenario_title,
                'content'=>$scenario_content,
            );
            $this->scenario_model->edit($data, 'id');
        }else{
            $groupMaxOrder = $this->scenario_model->getGroupMaxID($parent_id);
            if (empty($groupMaxOrder)) {
                $groupMaxOrder = 1;
            }else{
                $groupMaxOrder = $groupMaxOrder["group_order"] + 1;
            }

            $data = array(
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
                $parentData = $this->scenario_model->getOneByParam(array("id"=>$parent_id));
                $parent_tree_code = $parentData["tree_code"];
                $code = $parent_tree_code . sprintf("%'.04d", $groupMaxOrder);
                $this->scenario_model->edit(array("child_flag"=>1, "id"=>$parent_id), "id");
            }
            $this->scenario_model->edit(array("tree_code"=>$code, "id"=>$insert_id), "id");
        }

        redirect("admin/scenario/");
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
            $this->scenario_model->edit(array('id'=>$root['parent_id'],'child_flag'=>0),'id');
            return $this->output->set_output(json_encode( array('ok'=>true,'result'=>$result) ));
        }
        return $this->output->set_output(json_encode( array('ok'=>false,'result'=>$result) ));

    }

}
