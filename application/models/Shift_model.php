<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Shift_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'shifts';
        $this->primary_key = 'shift_id';
    }

    public function getWhere($cond){
        if (!empty($cond['organ_id'])){
            $this->db->where('shifts.organ_id', $cond['organ_id']);
        }
        if (!empty($cond['is_enable_reserve'])){
            $this->db->where("shift_type in (".SHIFT_STATUS_SUBMIT.",".SHIFT_STATUS_OUT.",".SHIFT_STATUS_REQUEST.",".SHIFT_STATUS_ME_REPLY.",".SHIFT_STATUS_ME_APPLY.",".SHIFT_STATUS_APPLY.")");
        }

        if (!empty($cond['is_booking'])){
            $this->db->where('epark_booking_id is not null');
        }

        if (!empty($cond['select_date'])){
            $this->db->where("from_time like '". $cond['select_date'] . " %'");
        }

        if (!empty($cond['epark_shift_id'])){
            $this->db->where("epark_shift_id", $cond['epark_shift_id']);
        }

        if (!empty($cond['out_from_time'])){
            $this->db->where("shifts.from_time <='". $cond['out_from_time'] ."'");
        }
        if (!empty($cond['out_to_time'])){
            $this->db->where("shifts.to_time >='". $cond['out_to_time'] ."'");
        }

        if (!empty($cond['in_from_time']) && !empty($cond['in_to_time'])){
            $this->db->where("to_time >'". $cond['in_from_time'] ."' and from_time <'". $cond['in_to_time'] ."'" );
        }

        if (!empty($cond['equal_from_time'])){
            $this->db->where("shifts.from_time" , $cond['equal_from_time']);
        }
        if (!empty($cond['equal_to_time'])){
            $this->db->where("shifts.to_time" , $cond['equal_to_time']);
        }
        if (!empty($cond['staff_id'])){
            $this->db->where("shifts.staff_id", $cond['staff_id']);
        }

        if (!empty($cond['is_booking'])){
            $this->db->where("epark_booking_id is not null");
        }
        if (!empty($cond['is_not_booking'])){
            $this->db->where("epark_booking_id is null");
        }
        if (!empty($cond['is_enable_apply'])){
            $this->db->where("shift_type in (".SHIFT_STATUS_ME_APPLY.",".SHIFT_STATUS_APPLY.")");
        }
        if (!empty($cond['is_disable_apply'])){
            $this->db->where("shift_type not in (".SHIFT_STATUS_ME_APPLY.",".SHIFT_STATUS_APPLY.")");
        }

    }

    public function getListByCond($cond){

        $this->db->from($this->table);
        $this->db->join('staffs', 'staffs.staff_id = shifts.staff_id', 'left');

        if (!empty($cond['staff_id'])){
            $this->db->where('shifts.staff_id', $cond['staff_id']);
        }
        if (!empty($cond['staff_sex'])){
            $this->db->where('staff_sex', $cond['staff_sex']);
        }

        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }
        if (!empty($cond['from_time'])){
            $this->db->where("from_time >='". $cond['from_time'] ."'");
        }
        if (!empty($cond['to_time'])){
            $this->db->where("to_time <='". $cond['to_time'] ."'");
        }
        if (!empty($cond['select_datetime'])){
            $this->db->where("from_time <='". $cond['select_datetime'] ."'");
            $this->db->where("to_time >'". $cond['select_datetime'] ."'");
        }
        if (!empty($cond['select_date'])){
            $this->db->where("from_time like '". $cond['select_date'] ." %'");
        }
        if (!empty($cond['in_from_time']) && !empty($cond['in_to_time'])){
            $this->db->where("((to_time >'". $cond['in_from_time'] ."' and from_time <'". $cond['in_to_time'] ."') || (from_time ='". $cond['in_from_time'] ."' and to_time ='". $cond['in_to_time'] ."'))" );
        }

        if (!empty($cond['include_from_time']) && !empty($cond['include_to_time'])){
            $this->db->where("from_time <='". $cond['include_from_time'] ."' and to_time>='". $cond['include_to_time'] ."'" );
        }

        if (!empty($cond['no_shift'])){
            $this->db->where("shift_id <>'". $cond['no_shift'] ."'");
        }
        if (!empty($cond['eq_from_time'])){
            $this->db->where("from_time", $cond['eq_from_time']);
        }
        if (!empty($cond['eq_to_time'])){
            $this->db->where("to_time", $cond['eq_to_time']);
        }

        if (!empty($cond['old_shift'])){
            $this->db->where("old_shift", NULL);
        }

        if (!empty($cond['no_edit'])){
            $this->db->where("shift_type in (".SHIFT_STATUS_REQUEST.",".SHIFT_STATUS_ME_APPLY.",".SHIFT_STATUS_APPLY.")");
        }

        if (!empty($cond['is_apply'])){
            $this->db->where("shift_type in (".SHIFT_STATUS_ME_APPLY.",".SHIFT_STATUS_APPLY.")");
        }

        if (!empty($cond['is_apply_enable'])){
            $this->db->where("shift_type in (".SHIFT_STATUS_SUBMIT.",".SHIFT_STATUS_REQUEST.",".SHIFT_STATUS_ME_REPLY.",".SHIFT_STATUS_ME_APPLY.",".SHIFT_STATUS_APPLY.")");
        }

        if (!empty($cond['reserve_flag'])){
            $this->db->where("shift_type in (".SHIFT_STATUS_OUT.",".SHIFT_STATUS_ME_APPLY.",".SHIFT_STATUS_APPLY.")");
        }

        $this->db->where('shifts.visible', '1');

        $this->db->order_by('shifts.staff_id, shifts.from_time');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getOtherOrgansShift($cond){
        $this->db->select('sum( HOUR(TIMEDIFF(to_time, from_time))*60+ MINUTE(TIMEDIFF(to_time, from_time))) as all_time');
        $this->db->from($this->table);

        if (!empty($cond['staff_id'])){
            $this->db->where('staff_id', $cond['staff_id']);
        }

        if (!empty($cond['cur_organ_id'])){
            $this->db->where('organ_id <> '.$cond['organ_id']);
        }

        $this->db->where("(from_time >='". $cond['from_time'] ."' and from_time <'". $cond['to_time'] ."') || (to_time >'". $cond['from_time'] ."' and to_time <'". $cond['from_time'] ."')");

        $this->db->where('visible', '1');

        $query = $this->db->get();

        $result = $query->row_array();

        return $result['all_time']==null ? 0 : $result['all_time'];
    }

    public function getRecordByCond($cond, $isOnlyShift=false){
		if ($isOnlyShift)
			$this->db->select($this->table.".*");
		else
			$this->db->select($this->table.".*, IF(staffs.staff_nick is NULL, 
					CONCAT(staffs.staff_first_name,' ', staffs.staff_last_name), 
					staffs.staff_nick
				) as staff_name");
        $this->db->from($this->table);
        $this->db->join('staffs', 'staffs.staff_id = shifts.staff_id', 'left');

        if (!empty($cond['shift_id'])){
            $this->db->where('shift_id', $cond['shift_id']);
        }

        if (!empty($cond['staff_id'])){
            $this->db->where('shifts.staff_id', $cond['staff_id']);
        }

        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }
        if (!empty($cond['from_time'])){
            $this->db->where("from_time >='". $cond['from_time'] ."'");
        }
        if (!empty($cond['to_time'])){
            $this->db->where("to_time <='". $cond['to_time'] ."'");
        }

        if (!empty($cond['shift_type'])){
            $this->db->where("shift_type", $cond['shift_type']);
        }

        if (!empty($cond['select_datetime'])){
            $this->db->where("from_time <='". $cond['select_datetime'] ."'");
            $this->db->where("to_time >'". $cond['select_datetime'] ."'");
        }
        if (!empty($cond['in_from_time']) && !empty($cond['in_to_time'])){
            $this->db->where("((to_time >'". $cond['in_from_time'] ."' and from_time <'". $cond['in_to_time'] ."') || (from_time ='". $cond['in_from_time'] ."' and to_time ='". $cond['in_to_time'] ."'))" );
        }

        $this->db->where('shifts.visible', '1');

        $query = $this->db->get();

        return $query->row_array();
    }
    public function isExist($organ_id, $staff_id, $shift_id, $from_time, $to_time){

        $this->db->from($this->table);

        if (!empty($staff_id)){
            $this->db->where('staff_id', $staff_id);
        }

        if (!empty($organ_id)){
            $this->db->where('organ_id', $organ_id);
        }

        $this->db->where("(('".$from_time."'<from_time and from_time <'". $to_time ."') or ('".$from_time."' < to_time and to_time <'". $to_time ."'))");

        if (!empty($shift_id)){
            $this->db->where("shift_id <>'". $shift_id ."'");
        }

        $query = $this->db->get();

        return !empty($query->result_array());
    }


    function getDivideShifts($from_time, $to_time, $organ_id){
        $sql = "select * 
from
(select from_time as time from shift_frames 
where from_time>='$from_time' and to_time<='$to_time' and organ_id='$organ_id'
union
select to_time as time from shift_frames 
where from_time>='$from_time' and to_time<='$to_time'and organ_id='$organ_id'
union
select from_time as time from shifts
where from_time>='$from_time' and to_time<='$to_time'and organ_id='$organ_id'
union
select to_time as time from shifts
where from_time>='$from_time' and to_time<='$to_time'and organ_id='$organ_id')
tmp
order by tmp.time
";
        $query = $this->db->query($sql);

        return $query->result_array();
    }

    public function getExistCount($cond){
        $this->db->from($this->table);
        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }
        if (!empty($cond['from_time'])){
            $this->db->where("from_time <='". $cond['from_time']. "'");
        }
        if (!empty($cond['to_time'])){
            $this->db->where("to_time >='". $cond['to_time']. "'");
        }

        $query = $this->db->get();

        return count($query->result_array());
    }

    public function getStaffShiftList($organ_id, $select_time){
        $this->db->select($this->table.".*, IF(staff_nick is NULL, CONCAT(staff_first_name,' ', staff_last_name), staff_nick) as staff_name");
        $this->db->from($this->table);

        $this->db->join('staffs', 'staffs.staff_id = shifts.staff_id', 'left');
        $this->db->where('organ_id', $organ_id);
        $this->db->where("from_time<='".$select_time."' and to_time > '".$select_time."'");

        $query = $this->db->get();
        return $query->result_array();
    }


    public function getReleationShift($organ_id, $staff_id, $time, $type){
        $this->db->select('*');
        $this->db->from($this->table);

        $this->db->where('organ_id', $organ_id);
        $this->db->where('staff_id', $staff_id);
        if ($type=='prev'){
            $this->db->where('to_time', $time);
        }
        if ($type=='next'){
            $this->db->where('from_time', $time);
        }

        $this->db->where('shift_type', 1);

        $query = $this->db->get();
        return $query->row_array();
    }


    public function isStaffActiveReserve($organ_id, $staff_id, $time){
        $this->db->select('*');
        $this->db->from($this->table);

        $this->db->where('organ_id', $organ_id);
        $this->db->where('staff_id', $staff_id);
        $this->db->where("from_time<='".$time."' and to_time>'".$time."'");

        $this->db->where('shift_type in ('.SHIFT_STATUS_APPLY.','.SHIFT_STATUS_ME_APPLY.')');

        $query = $this->db->get();
        return !empty($query->row_array());
    }

    public function getActiveStaffCount($organ_id, $time){
        $this->db->select('count(shift_id) as staff_count');
        $this->db->from($this->table);

        $this->db->where('organ_id', $organ_id);
        $this->db->where("from_time<='".$time."' and to_time>'".$time."'");

        $this->db->where('(shift_type = '. SHIFT_STATUS_APPLY .' or shift_type = '. SHIFT_STATUS_ME_APPLY . ')');

        $query = $this->db->get();
        $result = $query->row_array();
        return empty($result['staff_count']) ? 0 : $result['staff_count'];
    }

    public function isStaffRejectReserve($organ_id, $staff_id, $time){
        $this->db->select('*');
        $this->db->from($this->table);

        $this->db->where('organ_id', $organ_id);
        $this->db->where('staff_id', $staff_id);
        $this->db->where("from_time<='".$time."' and to_time>'".$time."'");
        $this->db->where('shift_type in (' . SHIFT_STATUS_REJECT.','.SHIFT_STATUS_REST.','.SHIFT_STATUS_OUT.')');

        $query = $this->db->get();
        return !empty($query->row_array());
    }

    public function getDayShift($organ_id, $select_date){
        $this->db->select($this->table.".*, 
            IF(staffs.staff_nick is NULL, 
                CONCAT(staffs.staff_first_name,' ', staffs.staff_last_name), 
                staffs.staff_nick
            ) as staff_name, staffs.staff_sex");
        $this->db->from($this->table);
        $this->db->join('staffs', 'shifts.staff_id=staffs.staff_id', 'left');

        $this->db->where('organ_id', $organ_id);
        $this->db->where("from_time like '" . $select_date . "%'");
        $this->db->where("to_time like '" . $select_date . "%'");

//        $this->db->where('shift_type', '-3');

        $this->db->order_by('from_time');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getShiftTimeListByAuth($date, $company_id, $auth, $paList){
        $this->db->select('shifts.staff_id, shifts.from_time, shifts.to_time, TIMESTAMPDIFF(MINUTE,shifts.from_time,shifts.to_time) as shift_time');
        $this->db->from($this->table);
        $this->db->join('organs', 'organs.organ_id = shifts.organ_id', 'left');
        $this->db->join('staffs', 'staffs.staff_id = shifts.staff_id', 'left');

        $this->db->where("shifts.from_time like '".$date."%'");
        $this->db->where("organs.company_id", $company_id);
        $this->db->where("shifts.shift_type", 2);
        $this->db->where("staffs.staff_auth", $auth);

        if (!empty($paList)){
            $this->db->where("staffs.staff_id in (".$paList.")");
        }

        $query = $this->db->get();

        return $query->result_array();
    }

    public function deleteDayShift($date, $staff_id, $organ_id, $type=''){
        $this->db->where("from_time like '".$date." %'");
        $this->db->where("staff_id", $staff_id);
        $this->db->where("organ_id", $organ_id);
        if (!empty($type))
            $this->db->where("shift_type", $type);

        $this->db->delete($this->table);

    }

	public function getShiftDataByParam($cond){
        $this->db->select('*');
        $this->db->from($this->table);

        if(!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }
        if(!empty($cond['staff_id'])){
            $this->db->where('staff_id', $cond['staff_id']);
        }
        if(!empty($cond['from_time']) && !empty($cond['to_time'])){
            $this->db->where("to_time > '" . $cond['from_time'] . "'");
        }

        if(!empty($cond['to_time'])){
            $this->db->where("from_time < '". $cond['to_time']."'");
        }

        if(!empty($cond['shift_type'])){
            $this->db->where("shift_type", $cond['shift_type']);
        }


        $query = $this->db->get();
        return $query->result_array();
    }

    public function getShiftTimeOverData($from_time, $to_time, $staffs){
        $this->db->select('shifts.staff_id,  (sum(TIMESTAMPDIFF(MINUTE, from_time, to_time)) - staffs.staff_shift*60) as diff');
        $this->db->from($this->table);
        $this->db->join('staffs', 'shifts.staff_id=staffs.staff_id', 'left');
        $this->db->where("from_time>='".$from_time."'");
        $this->db->where("to_time<='".$to_time."'");
        $this->db->where("shift_type in (9, 10)");
        $this->db->where("staffs.staff_id in (". $staffs . ")");
        $this->db->group_by('staffs.staff_id');
        $this->db->order_by('diff', 'desc');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getStaffShiftAllTime($staff_id) {
        $strSql = "SELECT staff_id, sum(TIMESTAMPDIFF(MINUTE, from_time, to_time)) as all_shift
                   FROM shifts 
                   WHERE (shift_type = 9 or shift_type=10) and staff_id=" . $staff_id . "
                   GROUP BY staff_id";

        $query = $this->db->query($strSql);

        return $query->row_array();
    }

    // 승인시간과 근무요청시간에 대한 계산을 진행한다.

    public function getStaffShiftTime($staff_id, $organ_id) {
        $strSql = " SELECT
                        SUM(staff_times) AS staff_times
                    FROM
                        (
                        SELECT
                            TIMESTAMPDIFF( MINUTE, from_time, to_time ) AS staff_times
                        FROM
                            shifts
                        WHERE
                            shift_type IN (1, 5, 9, 10)
                            AND staff_id = ". $staff_id ."
                            AND organ_id = ". $organ_id ."
                        ) AS t1
                    WHERE
                        t1.staff_times > 0";
        // $strSql = "SELECT staff_id, sum(shift_apply_time) as shift_apply_time, sum(shift_application_time) as shift_application_time FROM 
        //             (SELECT staff_id, TIMESTAMPDIFF(MINUTE, from_time, to_time) shift_apply_time, 0 AS shift_application_time
        //                 FROM shifts 
        //                 WHERE (shift_type = 9 or shift_type=10) and staff_id=" . $staff_id . " and organ_id=" . $organ_id . "
        //             UNION
        //             SELECT staff_id, 0 AS shift_apply_time, TIMESTAMPDIFF(MINUTE, from_time, to_time) shift_application_time
        //                 FROM shifts 
        //                 WHERE (shift_type = 1 or shift_type=5) and staff_id=" . $staff_id . " and organ_id=" . $organ_id . ") as final";

        $query = $this->db->query($strSql);

        return $query->row_array();
    }

    public function getStaffOtherShopShiftTime($organ_id, $staff_id) {
        
        $strSql = "select staff_id, sum(TIMESTAMPDIFF(MINUTE, from_time, to_time)) as all_shift, 0 as hope_time, 0 as reserve,  0 as submit, 0 as apply, 0 as pshift, 0 as rest, 0 as link
        from shifts 
        where shifts.organ_id<>".$organ_id." and (shift_type = 9 or shift_type=10) and staff_id=" . $staff_id . "
        group by staff_id";

        $query = $this->db->query($strSql);

        return $query->row_array();
    }

    public function getStaffOrderForAuto($organ_id, $from_time, $to_time, $in_from_time, $in_to_time, $type){

        $link_shift_where = "(from_time = '".$in_to_time."' or to_time = '".$in_from_time."')";
        if ($type == 'over') {
            $link_shift_where = "(from_time = '".$in_from_time."' or to_time = '".$in_to_time."')";
        }
        $strSql = "select ordered_res.* from 
                (select final.*
                    from 
                        (select tmp.staff_id, sum(tmp.all_shift - tmp.hope_time) as time, if(sum(tmp.reserve)>0, 1,0) as reserve, if(sum(tmp.submit)>0,1,0) as submit, if(sum(tmp.apply)>0,1,0) as apply, if(sum(tmp.pshift)>0,1,0) as pshift, if(sum(tmp.rest)>0, 1,0) as rest, if(sum(tmp.link)>0, 1,0) as link
                        from
                            (
                            select staffs.staff_id, 0 as all_shift,  staffs.staff_shift*60 as hope_time, 0 as reserve,  0 as submit, 0 as apply, 0 as pshift, 0 as rest, 0 as link
                            from staffs left join staff_organs on staffs.staff_id=staff_organs.staff_id
                            where staff_organs.organ_id=".$organ_id."
                        union
                            select staff_id, sum(TIMESTAMPDIFF(MINUTE, from_time, to_time)) as all_shift, 0 as hope_time, 0 as reserve,  0 as submit, 0 as apply, 0 as pshift, 0 as rest, 0 as link
                            from shifts 
                            where from_time >='".$from_time."' and to_time<= '".$to_time."' and shifts.organ_id=".$organ_id." and (shift_type = 9 or shift_type=10) 
                            group by staff_id
                        union
                            select reserves.staff_id,  0 as all_shift, 0 as hope_time, 1 as reserve, 0 as submit, 0 as apply, 0 as pshift, 0 as rest, 0 as link 
                                from reserves 
                                    where organ_id=".$organ_id." and
                                        ((reserve_exit_time >'".$in_from_time."' and reserve_time <'".$in_to_time."') || (reserve_time ='".$in_from_time."' and reserve_exit_time ='".$in_to_time."'))
                                        and reserve_status <= 2 and staff_id is not null
                        union 
                            select shifts.staff_id, 0 as all_shift, 0 as hope_time, 0 as reserve, 1 as submit, 0 as apply, 0 as pshift, 0 as rest, 0 as link from shifts
                            where organ_id = ".$organ_id." and 
                            ((from_time <'".$in_to_time."' and to_time >'".$in_from_time."') || (from_time ='".$in_from_time."' and to_time ='".$in_to_time."')) and shift_type = ".SHIFT_STATUS_SUBMIT."
                        union 
                            select shifts.staff_id, 0 as all_shift, 0 as hope_time, 0 as reserve, 0 as submit, 0 as apply, 0 as pshift, 1 as rest, 0 as link 
                                from shifts
                                    where organ_id <> ".$organ_id." and 
                                        ((from_time >'".$in_from_time."' and to_time <'".$in_to_time."') || (from_time ='".$in_from_time."' and to_time ='".$in_to_time."')) 
                                        and shift_type in (".SHIFT_STATUS_SUBMIT.", ".SHIFT_STATUS_REQUEST.", ".SHIFT_STATUS_ME_APPLY.", ".SHIFT_STATUS_APPLY.")
                        union 
                            select shifts.staff_id, 0 as all_shift, 0 as hope_time, 0 as reserve,0 as submit, 0 as apply, 1 as pshift, 0 as rest, 0 as link 
                                from shifts
                                    where organ_id= ".$organ_id." and ((from_time <'".$in_to_time."' and to_time >'".$in_from_time."') || (from_time ='".$in_from_time."' and to_time ='".$in_to_time."')) and shift_type in (".SHIFT_STATUS_REQUEST.")
                        union
                            select shifts.staff_id, 0 as all_shift, 0 as hope_time, 0 as reserve,0 as submit, 1 as apply, 1 as pshift, 0 as rest, 0 as link 
                                from shifts
                                    where organ_id= ".$organ_id." and ((from_time <'".$in_to_time."' and to_time >'".$in_from_time."') || (from_time ='".$in_from_time."' and to_time ='".$in_to_time."')) and shift_type in (".SHIFT_STATUS_ME_APPLY.", ".SHIFT_STATUS_APPLY.")
                        union
                            select shifts.staff_id, 0 as all_shift, 0 as hope_time, 0 as reserve, 0 as submit, 0 as apply, 0 as pshift, 1 as rest, 0 as link 
                                from shifts
                                    where organ_id = ".$organ_id." and from_time like '".substr($in_from_time, 0, 10)." %' and shift_type = ".SHIFT_STATUS_REST."
                        union
                            select shifts.staff_id, 0 as all_shift, 0 as hope_time, 0 as reserve, 0 as submit, 0 as apply, 0 as pshift, 0 as rest, 1 as link
                                from shifts
                                    where organ_id = ".$organ_id." and " . $link_shift_where . " and shift_type in (".SHIFT_STATUS_APPLY.", ".SHIFT_STATUS_ME_APPLY.")

                            ) as tmp
                    group by tmp.staff_id) as final
                    where final.rest = 0) ordered_res 
                    left join staffs on staffs.staff_id = ordered_res.staff_id";

        if ($type == "over"){
            $strSql .=" ORDER BY reserve asc, link desc, pshift desc, apply asc, staff_shift desc, time desc";
        }else{
            $strSql .=" ORDER BY apply desc, rest asc, reserve desc, link desc, submit desc, pshift desc, staff_shift desc, time asc ";
        }

        $query = $this->db->query($strSql);

        return $query->result_array();
    }

    public function getEnableShifts($organ_id, $from_time, $to_time){

        $this->db->select($this->table.'.*, staffs.staff_sex');
        $this->db->from($this->table);
        $this->db->join('staffs', 'staffs.staff_id = shifts.staff_id');

        $this->db->where('organ_id', $organ_id);
        $this->db->where('from_time<=', $from_time);
        $this->db->where('to_time>=', $to_time);
        $this->db->where("shift_type in (".SHIFT_STATUS_ME_APPLY.",".SHIFT_STATUS_APPLY.")");

        return $this->db->get()->result_array();

    }

    public function getRequestStaffs($organ_id, $from_time, $to_time){
        $strSql = "select final.*
                    from 
                        (select tmp.staff_id, sum(tmp.hope_time - tmp.all_shift) as time
                        from
                            (
                            select staffs.staff_id, 0 as all_shift,  staffs.staff_shift*60 as hope_time
                                from staffs left join staff_organs on staffs.staff_id=staff_organs.staff_id
                                where staff_organs.organ_id=".$organ_id."
                            union
                            select staff_id, sum(TIMESTAMPDIFF(MINUTE, from_time, to_time)) as all_shift, 0 as hope_time
                                from shifts 
                                where from_time >='".$from_time."' and to_time<= '".$to_time."' and shifts.organ_id=".$organ_id."
                            group by staff_id
                            
                            ) as tmp
                    group by tmp.staff_id) as final
                    where final.time>0";

        $query = $this->db->query($strSql);

        return $query->result_array();
    }

    public function isStaffInReject($staff_id, $organ_id, $from_time, $to_time){
        $this->db->from($this->table);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('organ_id', $organ_id);
        $this->db->where("from_time < '$to_time'");
        $this->db->where("to_time > '$from_time'");

        $this->db->where('shift_type in ('. SHIFT_STATUS_REJECT.','. SHIFT_STATUS_REST.','. SHIFT_STATUS_ME_REJECT.')');

        $query = $this->db->get();
        return !empty($query->row_array());
    }
    public function isStaffInRequest($staff_id, $organ_id, $from_time, $to_time){
        $this->db->from($this->table);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('organ_id', $organ_id);
        $this->db->where("from_time < '$to_time'");
        $this->db->where("to_time > '$from_time'");

        $this->db->where('shift_type in ('. SHIFT_STATUS_SUBMIT.','. SHIFT_STATUS_OUT.','. SHIFT_STATUS_ME_REPLY.')');

        $query = $this->db->get();
        return !empty($query->row_array());
    }
    public function isStaffInApply($staff_id, $organ_id, $from_time, $to_time){
        $this->db->from($this->table);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('organ_id', $organ_id);
        $this->db->where("from_time <= '$from_time'");
        $this->db->where("to_time >= '$to_time'");

        $this->db->where('shift_type in ('. SHIFT_STATUS_ME_APPLY.','. SHIFT_STATUS_APPLY.')');

        $query = $this->db->get();
        return !empty($query->row_array());
    }

    public function getReserveShiftStatus($organ_id, $staff_id, $from_time, $to_time){
        $shifts = $this->getListByCond([
            'organ_id' => $organ_id,
            'staff_id' => $staff_id,
            'in_from_time' => $from_time,
            'in_to_time' => $to_time,
            'reserve_flag' => 1
        ]);
        //ok Shifts.
        $links = [];
        $last = [];
        foreach ($shifts as $shift){
            if ($shift['shift_type'] == SHIFT_STATUS_OUT) continue;
            if (empty($last)){
                $last = $shift;
            }else{
                if ($last['to_time'] == $shift['from_time']){
                    $last['to_time'] = $shift['to_time'];
                }else{
                    $links[] = $last;
                    $last = $shift;
                }
            }
        }
        if (!empty($last)) $links[] = $last;
        foreach ($links as $link){
            if ($link['from_time']<=$from_time && $link['to_time']>=$to_time) return RESERVE_CONDITION_OK;
        }

        //enable Shifts.
        $links = [];
        $last = [];
        foreach ($shifts as $shift){
            if (empty($last)){
                $last = $shift;
            }else{
                if ($last['to_time'] == $shift['from_time']){
                    $last['to_time'] = $shift['to_time'];
                }else{
                    $links[] = $last;
                    $last = $shift;
                }
            }
        }
        if (!empty($last)) $links[] = $last;

        foreach ($links as $link){
            if ($link['from_time']<=$from_time && $link['to_time']>=$to_time) return RESERVE_CONDITION_ENABLE;
        }

        return RESERVE_CONDITION_DISABLE;
    }

    public function getErrorShift(){
        $sql = "
            SELECT tmp1.* from
            (SELECT count(shift_id) as row, GROUP_CONCAT(shift_id) as shift_ids FROM shifts group by organ_id, staff_id, from_time) tmp1
            where tmp1.row>1
            union
            SELECT tmp2.* from
            (SELECT count(shift_id) as row, GROUP_CONCAT(shift_id) as shift_ids FROM shifts group by organ_id, staff_id, to_time) tmp2
            where tmp2.row>1
        ";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getShiftDataFromId($shift_id){
        $this->db->select("shifts.*, organs.organ_name, IF(staffs.staff_nick is NULL, CONCAT(staffs.staff_first_name,' ', staffs.staff_last_name), staffs.staff_nick) as staff_name");
        $this->db->from($this->table);
        $this->db->join('staffs', 'shifts.staff_id = staffs.staff_id', 'left');
        $this->db->join('organs', 'organs.organ_id = shifts.organ_id', 'left');
        $this->db->where('shift_id', $shift_id);
        $query = $this->db->get();

        return $query->row_array();
    }

    public function getMaxShiftDate($cond){
        $this->db->select('max(to_time) as end_date');
        $this->db->from($this->table);
        $this->getWhere($cond);

        $query = $this->db->get();
        $result = $query->row_array();
        if (empty($result)) return date('Y-m-d');

        $end_date = new DateTime($result['end_date']);
        return $end_date->format('Y-m-d');
    }

    public function getListDataByCond($cond){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->getWhere($cond);

        $query = $this->db->get();
        return $query->result_array();
    }
    public function getRowDataByCond($cond){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->getWhere($cond);

        $query = $this->db->get();
        return $query->row_array();
    }

    public function deleteNoEparkDelete($organ_id, $select_date, $staff_id){
        
        $this->db->where('organ_id', $organ_id);
        $this->db->where('staff_id', $staff_id);
        $this->db->where("from_time like '" . $select_date . " %'");
        $this->db->where("epark_shift_id is null");
        $this->db->delete($this->table);

    }
    
    public function deleteEparkDeleteData($organ_id, $select_date, $exist_staffs){
        
        $this->db->where('organ_id', $organ_id);
        if(!empty($exist_staffs))
            $this->db->where('staff_id not in (' . implode(',', $exist_staffs). ')');
        $this->db->where("from_time like '" . $select_date . " %'");
        $this->db->delete($this->table);
    }  

    public function deleteEparkEmptyApply($epark_shift_id){
        
        $this->db->where('epark_shift_id', $epark_shift_id);
        $this->db->where("epark_booking_id is not null");
        $this->db->delete($this->table);

    }

    public function getShiftList($cond){
        $this->db->select('shifts.*, shift_status.*');
        $this->db->from($this->table);
        $this->db->join('shift_status', 'shifts.shift_type = shift_status.id', 'left');
        
        $this->getWhere($cond);

        $this->db->order_by('from_time');
        $query = $this->db->get();
        return $query->result_array();
    }

}
