<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';
class Bot_model extends Base_model
{

    public function __construct()
    {
        parent::__construct();

    }

    function getAnalysisData($company_id=''){

        $this->db->select('count(customer_id) as visit',false);
        $this->db->select_sum('scenario');
        $this->db->select_sum('chat');
        $this->db->select_sum('faq');
        $this->db->from('tbl_customer');
        if($company_id) {
            $this->db->where('company_id', $company_id);
//            $this->db->group_by("company_id");
        }
        $query = $this->db->get();

        $result = $query->row_array();
        return $result;
    }
    
    function getCustomer($session)
    {
        $this->db->select('*');
        $this->db->from('tbl_customer');
        $this->db->where('session', $session);
        $query = $this->db->get();

        $user = $query->row_array();

        if(!empty($user)){
            return $user;
        } else {
            return array();
        }
    }

    function addCustomer($user)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_customer', $user);
        $this->db->trans_complete();
    }

    function updateCustomer($data,$key)
    {
        $data['visit_time'] =date('Y-m-d H:i:s');
        $this->db->set($key, $data[$key], FALSE);
        $this->db->where('session', $data['session']);
        $this->db->update('tbl_customer');
    }

    function addSearchText($text,$company_id){

        $item = $this->db->select('*')->from('tbl_search')->where('search_text', $text)->where('company_id', $company_id)->get()->row();

        if(!empty($item)){
            $this->db->query("UPDATE tbl_search SET search_cnt = search_cnt+1 where search_text='".$text."' and company_id=".$company_id);
        } else {
            $this->db->insert('tbl_search', array('search_text'=>$text,'company_id'=>$company_id,'search_cnt'=>1));
        }

    }

    function visitScenario($company_id)
    {
        $session_id = session_id();
        $user = $this->getCustomer($session_id,$company_id);

        if(empty($user)){
            $this->load->library('user_agent');
            $user = array(
                'company_id'=>$company_id,
                'session'=>$session_id,
                'client_ip'=>$this->input->ip_address(),
                'agent'=>$this->agent->agent_string(),
                'browser'=>getBrowserAgent(),
                'visit_time'=>date('Y-m-d H:i:s'),
                'scenario'=>1
            );
            $this->addCustomer($user);
        }else{
            $data = array(
                'session'=>$session_id,
                'company_id'=>$company_id,
                'scenario'=>'scenario+1',
            );
            $this->updateCustomer($data,'scenario');
        }

    }

    function visitFaq($company_id)
    {
        $session_id = session_id();
        $user = $this->getCustomer($session_id,$company_id);

        if(empty($user)){
            $this->load->library('user_agent');
            $user = array(
                'company_id'=>$company_id,
                'session'=>$session_id,
                'client_ip'=>$this->input->ip_address(),
                'agent'=>$this->agent->agent_string(),
                'browser'=>getBrowserAgent(),
                'visit_time'=>date('Y-m-d H:i:s'),
                'faq'=>1
            );

            $this->addCustomer($user);
        }else{
            $data = array(
                'session'=>$session_id,
                'company_id'=>$company_id,
                'faq'=>'faq+1',
            );
            $this->updateCustomer($data,'faq');
        }
    }
    function visitChat($company_id)
    {
        $session_id = session_id();
        $user = $this->getCustomer($session_id,$company_id);

        if(empty($user)){
            $this->load->library('user_agent');
            $user = array(
                'company_id'=>$company_id,
                'session'=>$session_id,
                'client_ip'=>$this->input->ip_address(),
                'agent'=>$this->agent->agent_string(),
                'browser'=>getBrowserAgent(),
                'visit_time'=>date('Y-m-d H:i:s'),
                'chat'=>1,
            );

            $this->addCustomer($user);
        }else{
            $data = array(
                'session'=>$session_id,
                'company_id'=>$company_id,
                'chat'=>'chat+1',
            );
            $this->updateCustomer($data,'chat');
        }
    }
}