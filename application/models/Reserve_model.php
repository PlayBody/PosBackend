<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Reserve_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'reserves';
        $this->primary_key = 'reserve_id';
    }

    public function isExistMyReserve($user_id, $organ_id, $time){
    	$this->db->select('*');
    	$this->db->from($this->table);

    	$this->db->where('user_id', $user_id);
    	$this->db->where('organ_id', $organ_id);

    	$this->db->where("start_time<='".$time."' and end_time>'".$time."'");

    	$query = $this->db->get();
    	$result = $query->row_array();

    	return !empty($result);
    }

    public function getReserveCount($organ_id, $time, $to_time, $staff_id = ''){
    	$this->db->select('count(reserve_id) as count');
    	$this->db->from($this->table);

    	$this->db->where('organ_id', $organ_id);
    	$this->db->where("(reserve_time<='". $to_time. "') AND (ADDDATE(reserve_exit_time, INTERVAL sum_interval MINUTE) >'".$time."')");
    	if (!empty($staff_id)){
            $this->db->where("staff_id", $staff_id);
        }

    	$query = $this->db->get();
    	$result = $query->row_array();

    	$count=0;
    	if (!empty($result['count'])) $count = $result['count'];

    	return $count;
    }


    public function getReservePeriodCount($organ_id, $from_time, $to_time, $staff_id = ''){
        $this->db->select('count(reserve_id) as count');
        $this->db->from($this->table);

        $this->db->where('organ_id', $organ_id);

        $this->db->where("((reserve_time<='". $from_time. "' and reserve_exit_time>'".$from_time."') OR (reserve_time<'". $to_time. "' and reserve_exit_time>='".$to_time."'))");
//        $this->db->where("reserve_time<'". $to_time. "' and reserve_exit_time>='".$to_time."'");
        if (!empty($staff_id)){
            $this->db->where("staff_id", $staff_id);
        }

        $query = $this->db->get();
        $result = $query->row_array();

        $count=0;
        if (!empty($result['count'])) $count = $result['count'];

        return $count;
    }

    public function isMyPeriodReserve($user_id, $from_time, $to_time){
        $this->db->from($this->table);

        $this->db->where('user_id', $user_id);

        $this->db->where("((reserve_time<='". $from_time. "' and reserve_exit_time>'".$from_time."') OR (reserve_time<'". $to_time. "' and reserve_exit_time>='".$to_time."'))");

        $query = $this->db->get();
        $result = $query->row_array();

        return !empty($result);
    }

    public function isExistStaff($staff_id, $time){
    	$this->db->select('*');
    	$this->db->from($this->table);

    	$this->db->where('staff_id', $staff_id);

    	$this->db->where("start_time<='".$time."' and end_time>'".$time."'");

    	$query = $this->db->get();
    	$result = $query->row_array();

    	return !empty($result);
    }

    public function getListByCond($cond){
        $this->db->select($this->table.".*, organs.organ_name, users.user_first_name, users.user_last_name, IF(staffs.staff_nick is NULL, 
                CONCAT(staffs.staff_first_name,' ', staffs.staff_last_name), 
                staffs.staff_nick
            ) as staff_name");
        $this->db->from($this->table);
        $this->db->join('organs', 'organs.organ_id = reserves.organ_id');
        $this->db->join('staffs', 'staffs.staff_id = reserves.staff_id', 'left');
        $this->db->join('users', 'users.user_id = reserves.user_id');


        if (!empty($cond['reserve_id'])){
            $this->db->where('reserves.reserve_id', $cond['reserve_id']);
        }

        if (!empty($cond['staff_id'])){
            $this->db->where('reserves.staff_id', $cond['staff_id']);
        }
        if (!empty($cond['from_time'])){
            $this->db->where("reserve_time>='". $cond['from_time']."'");
        }
        if (!empty($cond['to_time'])){
            $this->db->where("reserve_exit_time<='". $cond['to_time']."'");
        }
        if (!empty($cond['reserve_time'])){
            $this->db->where("reserve_time", $cond['reserve_time']);
        }
        if (!empty($cond['select_date'])){
            $this->db->where("reserve_time like '" . $cond['select_date'] . " %'");
        }
        if (!empty($cond['user_id'])){
            $this->db->where("reserves.user_id", $cond['user_id']);
        }
        if (!empty($cond['company_id'])){
            $this->db->where("organs.company_id",  $cond['company_id']);
        }
        if (!empty($cond['organ_ids'])){
            $this->db->where("reserves.organ_id in (". $cond['organ_ids'] .")");
        }
        if (!empty($cond['organ_id'])){
            $this->db->where("reserves.organ_id", $cond['organ_id']);
        }
        if (!empty($cond['reserve_status'])){
            $this->db->where("reserves.reserve_status" , $cond['reserve_status']);
        }
        if (!empty($cond['max_status'])){
            $this->db->where("reserves.reserve_status  <= " . $cond['max_status']);
        }
        if (!empty($cond['in_from_time']) && !empty($cond['in_to_time'])){
            $this->db->where("((reserve_exit_time >'". $cond['in_from_time'] ."' and reserve_time <'". $cond['in_to_time'] ."') || (reserve_time ='". $cond['in_from_time'] ."' and reserve_exit_time ='". $cond['in_to_time'] ."'))" );
        }


        $this->db->order_by($this->table.'.reserve_time', 'asc');
        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function getOtherReserveCount($cond){
        $this->db->select('reserve_time, count(reserve_id) as count');
        $this->db->from($this->table);

        if (!empty($cond['from_time'])){
            $this->db->where("reserve_time>='". $cond['from_time']."'");
        }
        if (!empty($cond['to_time'])){
            $this->db->where("reserve_time<='". $cond['to_time']."'");
        }

        if (!empty($cond['staff_id'])){
            $this->db->where('staff_id', $cond['staff_id']);
        }
        if (!empty($cond['user_id'])){
            $this->db->where('user_id<>'.$cond['user_id']);
        }

        $this->db->group_by('reserve_time');

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function getReserveStaffs($organ_id, $from_time, $to_time){
        $this->db->from($this->table);
        $this->db->where('organ_id', $organ_id);
        $this->db->where("reserve_time >='".$from_time."' and reserve_time<'".$to_time."'");

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getMonthReserves($staff_id, $month){
        $this->db->from($this->table);
        $this->db->where('staff_id', $staff_id);
        $this->db->where("reserve_time like '%".$month."-%'");

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getReserveList($cond){
        $this->db->select($this->table.".*, 
            IF(staffs.staff_nick is NULL, 
                CONCAT(staffs.staff_first_name,' ', staffs.staff_last_name), 
                staffs.staff_nick
            ) as staff_name, 
            staffs.staff_sex,
            IF(users.user_first_name is NULL, 
                users.user_nick,
                CONCAT(users.user_first_name,' ', users.user_last_name)
            ) as user_name, users.user_sex");
        $this->db->from($this->table);
        $this->db->join('staffs', 'reserves.staff_id=staffs.staff_id', 'left');
        $this->db->join('users', 'reserves.user_id=users.user_id', 'left');

        if (!empty($cond['staff_id']))
            $this->db->where($this->table.'.staff_id', $cond['staff_id']);

        if (!empty($cond['organ_id']))
            $this->db->where('organ_id', $cond['organ_id']);

        if (!empty($cond['from_time']))
            $this->db->where("reserve_time >= '".$cond['from_time']."'");

        if (!empty($cond['to_time']))
            $this->db->where("reserve_exit_time <= '". $cond['to_time']."'");

        if (!empty($cond['select_date']))
            $this->db->where("reserve_time like '". $cond['select_date']." %'");

        if (!empty($cond['reserve_status']))
            $this->db->where("reserve_status", $cond['reserve_status']);

        if (!empty($cond['max_status']))
            $this->db->where("reserve_status<=". $cond['max_status']);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getReserveRecord($cond){
        $this->db->from($this->table);
        if (!empty($cond['staff_id']))
            $this->db->where('staff_id', $cond['staff_id']);

        if (!empty($cond['organ_id']))
            $this->db->where('organ_id', $cond['organ_id']);

        if (!empty($cond['reserve_time']))
            $this->db->where("reserve_time", $cond['reserve_time']);

        if (!empty($cond['reserve_exit_time']))
            $this->db->where("reserve_exit_time", $cond['reserve_exit_time']);

        $query = $this->db->get();
        return $query->row_array();

    }

    public function getReserverLastRecord($cond){

        $this->db->from($this->table);
        if (!empty($cond['staff_id']))
            $this->db->where('staff_id', $cond['staff_id']);

        if (!empty($cond['organ_id']))
            $this->db->where('organ_id', $cond['organ_id']);

        if (!empty($cond['user_id']))
            $this->db->where('user_id', $cond['user_id']);

        $this->db->order_by('create_date', 'desc');
        $query = $this->db->get();

        return $query->row_array();
    }


    public function getReserveNowData($cond){
        $this->db->from($this->table);
        if (!empty($cond['staff_id']))
            $this->db->where('staff_id', $cond['staff_id']);

        if (!empty($cond['organ_id']))
            $this->db->where('organ_id', $cond['organ_id']);

        if (!empty($cond['from_time']))
            $this->db->where("reserve_time >= '".$cond['from_time']."'");

        if (!empty($cond['to_time']))
            $this->db->where("reserve_time <= '". $cond['to_time']."'");

        if (!empty($cond['reserve_status']))
            $this->db->where("reserve_status", $cond['reserve_status']);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getVisitCount($organ_id, $user_id, $company_id){
        if($company_id==1){
            $now_h = date('G');
            $now_date = date('Y-m-d');
            $dt = new DateTime($now_date);

            if($now_h<12){
                $dt->sub(new DateInterval("P1D")); // 2016-03-02
                $fromDate = $dt->format("Y-m-d");
                $from_time = $fromDate . ' 12:00:00';
                $to_time = $now_date . ' 12:00:00';
            }else{
                $dt->add(new DateInterval("P1D")); // 2016-03-02
                $toDate = $dt->format("Y-m-d");
                $from_time = $now_date . ' 12:00:00';
                $to_time = $toDate . ' 12:00:00';
            }
        }else{
            $from_time = date('Y-m-d') . ' 00:00:00';
            $to_time = date('Y-m-d') . ' 23:59:59';
        }

        $this->db->select('count(reserve_id) as cnt');
        $this->db->from($this->table);
        $this->db->where('organ_id', $organ_id);
        $this->db->where('user_id', $user_id);
        $this->db->where("visit_time >='" . $from_time . "'");
        $this->db->where("visit_time <'" . $to_time . "'");

        $query = $this->db->get();

        $result = $query->row_array();
        return empty($result['cnt']);

    }
    public function getFreeReserve($organ_id, $date){
        $this->db->from($this->table);
        $this->db->where('staff_id', null);
        $this->db->where('organ_id', $organ_id);
        $this->db->where("reserve_time like '" . $date . " %'");
        $this->db->where("reserve_status <= 2");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getLeastReserveStaff($organ_id, $date, $staff_id){
        $this->db->select("staff_shift_sorts.show_staff_id, count(reserve_id) as reserve_count");
        $this->db->from($this->table);
        $this->db->join('staff_shift_sorts', 'reserves.staff_id=staff_shift_sorts.show_staff_id', 'right');

        $this->db->where('staff_shift_sorts.staff_id', $staff_id);
        $this->db->where('reserves.staff_id is not null');

        $this->db->where('organ_id', $organ_id);
        $this->db->where("reserve_time like '" . $date . " %'");
        $this->db->where("reserve_status <= 2");
        $query = $this->db->group_by('staff_shift_sorts.show_staff_id');
        $query = $this->db->order_by('reserve_count');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getReserveInputData($cond){
        $this->db->from($this->table);
        if (!empty($cond['organ_id']))
            $this->db->where('organ_id', $cond['organ_id']);

        if (!empty($cond['user_id']))
            $this->db->where("user_id", $cond['user_id']);

        if (!empty($cond['reserve_status']))
            $this->db->where("reserve_status", $cond['reserve_status']);

        if (!empty($cond['now_time'])) {
            $this->db->where("date_add(reserve_time,interval 30 minute) >= '" . $cond['now_time'] . "'");
            $this->db->where("date_sub(reserve_time,interval 30 minute) <= '" . $cond['now_time'] . "'");
        }

        $query = $this->db->get();
        return $query->row_array();
    }


}
