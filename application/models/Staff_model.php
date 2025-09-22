<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Staff_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'staffs';
        $this->primary_key = 'staff_id';
    }

    function login($data)
    {
        if(empty($data['login_id']) || empty($data['password'])) {
            return false;
        }

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('staff_mail',$data['login_id']);
        $this->db->where('staff_password',sha1($data['password']));
        $this->db->where("visible", 1);
        return $this->db->get()->row_array();
    }


    function getStaffList($cond)
    {
        $this->db->select("*, staff_auth as auth, IF(staff_nick is NULL, CONCAT(staff_first_name,' ', staff_last_name), staff_nick) as sort_name");
        $this->db->from($this->table);

        if (!empty($cond['staff_mail']))
            $this->db->where("staff_mail", $cond['staff_mail']);
        if (!empty($cond['company_id']))
            $this->db->where("company_id", $cond['company_id']);
        if (!empty($cond['staff_auth']))
            $this->db->where("staff_auth>=". $cond['staff_auth']);

        $this->db->order_by('sort_no', 'asc');
        return $this->db->get()->result_array();
    }

    function getStaffsByOrganId($organ_id){
        $this->db->from($this->table);
        $this->db->where('visible', '1');

        $this->db->where("staff_belongs like '%_".$organ_id."_%'");
        $this->db->order_by('sort_no', 'asc');

        $query = $this->db->get();
        return $query->result_array();
    }

    function isMailCheck($email, $staff_id){
        $this->db->from($this->table);
        $this->db->where('staff_mail', $email);

        if (!empty($staff_id)){
            $this->db->where("staff_id <>".$staff_id);
        }

        $query = $this->db->get();

        return empty($query->row_array());
    }

    public function getSortMax(){
        $this->db->select('max(sort_no) as sort');
        $this->db->from($this->table);

        $query = $this->db->get();

        $result = $query->row_array();

        $sort = empty($result['sort']) ? 0 : $result['sort'];
        return $sort+1;
    }

    public function getStaffs($cond){
        $this->db->select($this->table.'.*');
        $this->db->from($this->table);
        $this->db->join('staff_organs', 'staff_organs.staff_id = staffs.staff_id');

        if (!empty($cond['staff_sex'])){
            $this->db->where('staff_sex', $cond['staff_sex']);
        }
        if (!empty($cond['organ_id'])){
            $this->db->where('staff_organs.organ_id', $cond['organ_id']);
        }
        if (!empty($cond['min_auth'])){
            $this->db->where('staffs.staff_auth >=', $cond['min_auth']);
        }
        if (!empty($cond['max_auth'])){
            $this->db->where('staffs.staff_auth <=', $cond['max_auth']);
        }
        if (!empty($cond['company_id'])){
            $this->db->where('staffs.company_id', $cond['company_id']);
        }

        $this->db->order_by('sort_no', 'asc');

        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * new.list
     */
    function getPosStaffListByOrgan($organ_id, $auth)
    {
        $this->db->select("*, staff_auth as auth, IF(staff_nick is NULL, CONCAT(staff_first_name,' ', staff_last_name), staff_nick) as sort_name");
        $this->db->from($this->table);
        $this->db->join('staff_organs', 'staff_organs.staff_id = staffs.staff_id');

        $this->db->where('organ_id', $organ_id);
        $this->db->where('staff_auth1 <'.$auth);

        $this->db->order_by('sort_no', 'asc');
        return $this->db->get()->result_array();
    }

    function getStaffHopTime($staff_id) {
        $strSql = "SELECT staffs.staff_shift*60 as hope_time 
                   FROM staffs 
                   WHERE staff_id=" . $staff_id;

        $query = $this->db->query($strSql);

        return $query->row_array();
    }

    /* new function */
    public function getListData($cond){
        $this->db->select("staffs.*");

        $this->db->from($this->table);
        $this->db->join('staff_organs', 'staff_organs.staff_id = staffs.staff_id');

        $this->getWhere($cond);

        $this->db->group_by('staffs.staff_id');
        
        return $this->db->get()->result_array();
    }

    private function getWhere($cond){
        if (!empty($cond['organ_id'])){
            $this->db->where('staff_organs.organ_id', $cond['organ_id']);
        }
        if (empty($cond['have_guest'])){
            $this->db->where('staffs.staff_auth > 0');
        }
        if (empty($cond['have_admin'])){
            $this->db->where('staff_auth < ' . STAFF_AUTH_ADMIN);
        }

    }

}
