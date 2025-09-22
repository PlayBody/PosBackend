<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Slip extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staffs/staff_attend_model');
        $this->load->model('staff_organ_model');
        $this->load->model('master/mst_point_rate_special_limit_model');
        $this->load->model('master/mst_point_rate_special_period_model');
        $this->load->model('organ_model');
        $this->load->model('order_model');
        $this->load->model('staff_point_setting_model');
        $this->load->model('staff_point_add_model');
    }

    public function loadPaySlipMonth(){
        $staff_id = $this->input->post('staff_id');
        $date_year = $this->input->post('date_year');
        $date_month = $this->input->post('date_month');

        $from_datetime = new DateTime($date_year.'-'.$date_month.'-01');
        $from_time =  $from_datetime->format('Y-m-d H:i:s');
        $from_date =  $from_datetime->format('m-d');

        $to_datetime = new DateTime($date_year.'-'.$date_month.'-01');
        $to_datetime->add(new DateInterval('P1M'))->sub(new DateInterval('PT1S'));
        $to_time =  $to_datetime->format('Y-m-d H:i:s');
        $to_date =  $to_datetime->format('m-d');

        $staff = $this->staff_model->getFromId($staff_id);
        $staff_response_type = empty($staff['menu_response']) ? 3 : $staff['menu_response'];
        $staff_grade = empty($staff['staff_grade_level']) ? '' : $staff['staff_grade_level'];
        $staff_worked_year = $this->getStaffWorkedYear($staff['staff_entering_date'], $to_time);

        $auth = empty($staff['staff_auth']) ? 1 : $staff['staff_auth'];

        $organs = $this->getOrgansByStaffPermission($staff_id);
        $sum_amount = 0;

        $point_data = [];

        foreach ($organs as $organ){
            $attends = $this->staff_attend_model->getAttendData($staff_id, $organ['organ_id'], $from_time, $to_time);
            $attend_time = $this->getAllAttendTime($attends, $from_time, $to_time);
//            $sum_attend += $attend_time/60;

            $reserves = $this->order_model->getCompleteOrdersByStaff($staff_id, $organ['organ_id'], $from_time, $to_time);
            $reserve_time = $this->getAllReserveTime($reserves, $from_time, $to_time);
//            $sum_reserve += $reserve_time/60;

            $organ_work_hour_point = (empty($organ['attend_point']) ? 0 : $organ['attend_point']) * 60;
            $work_time_point = $organ_work_hour_point * intval($attend_time/60);

            $organ_reserve1_hour_point = (empty($organ['reserve_menu_response_1_point']) ? 0 : $organ['reserve_menu_response_1_point']) * 60;
            $organ_reserve2_hour_point = (empty($organ['reserve_menu_response_2_point']) ? 0 : $organ['reserve_menu_response_2_point']) * 60;
            $organ_reserve_hour_point = 0;
            if($staff_response_type=='1'){
                $organ_reserve_hour_point = $organ_reserve1_hour_point;
            }elseif($staff_response_type=='2'){
                $organ_reserve_hour_point = $organ_reserve2_hour_point;
            }
            $reserve_time_point = $organ_reserve_hour_point * intval($reserve_time/60);

            /* grade rate */
            $organ_grade_rate = 0;
            if($staff_grade==''){
                $organ_grade_rate = (empty($organ['grade_1_point']) ? 0 : $organ['grade_1_point']);
            }elseif($staff_grade=='1'){
                $organ_grade_rate = (empty($organ['grade_2_point']) ? 0 : $organ['grade_2_point']);
            }elseif($staff_grade=='2'){
                $organ_grade_rate = (empty($organ['grade_3_point']) ? 0 : $organ['grade_3_point']);
            }

            /* entering year rate */
            $organ_entering_rate = 0;
            if($staff_worked_year<1){
                $organ_entering_rate = (empty($organ['entering_1_point']) ? 0 : $organ['entering_1_point']);
            }elseif($staff_worked_year<3){
                $organ_entering_rate = (empty($organ['entering_2_point']) ? 0 : $organ['entering_2_point']);
            }elseif($staff_worked_year<5){
                $organ_entering_rate = (empty($organ['entering_3_point']) ? 0 : $organ['entering_3_point']);
            }elseif($staff_worked_year<10){
                $organ_entering_rate = (empty($organ['entering_4_point']) ? 0 : $organ['entering_4_point']);
            }else{
                $organ_entering_rate = (empty($organ['entering_5_point']) ? 0 : $organ['entering_5_point']);
            }

            /* special rate */
            $specials = $this->mst_point_rate_special_period_model->getDataByParam(['organ_id'=>$organ['organ_id']]);
            $special_rate = 1;
            foreach ($specials as $special){
                if ($special['from_date']>$special['to_date']){
                    if ($special['from_date']>=$from_date && $special['from_date']<=$to_date){
                        $special_from = $from_datetime->format('Y').'-'.$special['from_date'].' 00:00:00';
                        $special_to = ($from_datetime->format('Y')+1).'-'.$special['to_date'].' 23:59:59';
                    }elseif($special['to_date']>=$from_date && $special['to_date']<=$to_date){
                        $special_from = ($from_datetime->format('Y')-1).'-'.$special['from_date'].' 00:00:00';
                        $special_to = $from_datetime->format('Y').'-'.$special['to_date'].' 23:59:59';
                    }else{
                        continue;
                    }

                }else{
                    if ($special['from_date']<$to_date && $special['to_date']>$from_date){
                        $special_from = $from_datetime->format('Y').'-'.$special['from_date'].' 00:00:00';
                        $special_to = $from_datetime->format('Y').'-'.$special['to_date'].' 23:59:59';
                    }else{
                        continue;
                    }
                }

                $special_attends = $this->staff_attend_model->getAttendData($staff_id, $organ['organ_id'], $special_from, $special_to);
                $days = $this->getAttendDays($special_attends, $special_from, $special_to);
                if ($special['rate_days']<=$days) $special_rate *= (1 + $special['rate']);
            }

            $work_time_limit_over_setting = $this->mst_point_rate_special_limit_model->getDataByParam(['organ_id'=>$organ['organ_id'], 'type'=>2]);
            $work_time_limit_rate = 0;
            if (!empty($work_time_limit_over_setting[0]['value']) && intval($attend_time/60)>=$work_time_limit_over_setting[0]['value']){
                $work_time_limit_rate = $work_time_limit_over_setting[0]['rate'];
            }else{
                $work_time_limit_min_setting = $this->mst_point_rate_special_limit_model->getDataByParam(['organ_id'=>$organ['organ_id'], 'type'=>1]);
                if (!empty($work_time_limit_min_setting[0]['value']) && intval($attend_time/60)>=$work_time_limit_min_setting[0]['value']){
                    $work_time_limit_rate = $work_time_limit_min_setting[0]['rate'];
                }
            }

            $work_day_limit_setting = $this->mst_point_rate_special_limit_model->getDataByParam(['organ_id'=>$organ['organ_id'], 'type'=>3]);
            $work_day_limit_rate = 0;
            if (!empty($work_day_limit_setting[0]['value'])){
                $days = $this->getAttendDays($attends, $from_time, $to_time);
                if($days>=$work_day_limit_setting[0]['value']) $work_day_limit_rate = $work_day_limit_setting[0]['rate'];
            }

            $reserve_time_limit_setting = $this->mst_point_rate_special_limit_model->getDataByParam(['organ_id'=>$organ['organ_id'], 'type'=>4]);
            $reserve_time_limit_rate = 0;
            if (!empty($reserve_time_limit_setting[0]['value'])){
                if(intval($reserve_time/60)>=$reserve_time_limit_rate[0]['value']) $reserve_time_limit_rate = $reserve_time_limit_rate[0]['rate'];
            }

            /*get add point*/
            $add_points = $this->staff_point_add_model->getStaffAddPoints([
                'organ_id'=>$organ['organ_id'],
                'staff_id'=>$staff_id,
                'status' => 2,
                'from_date' => $from_datetime->format('Y-m-d'),
                'to_date' => $to_datetime->format('Y-m-d'),
            ]);

            $sum_add_point = 0;
            foreach ($add_points as $add_point){
                $sum_add_point += $add_point['point_weight'] * $add_point['value'];
            }


            $sum_point = $work_time_point + $reserve_time_point + $sum_add_point;
            $rate  = (1 + $organ_grade_rate + $organ_entering_rate) * $special_rate * (1 + $work_time_limit_rate) * (1 + $work_day_limit_rate) * (1 + $reserve_time_limit_rate);

            $organ_record = [];
            $organ_record['organ'] = $organ;
            $organ_record['attend_time'] = intval($attend_time/60);
            $organ_record['reserve_time'] = intval($reserve_time/60);
            $organ_record['default_amount'] = $sum_point;
            $organ_record['monthly_amount'] = intval($sum_point * $rate);
            $organ_record['rate'] = $rate;
            $organ_record['sum_add_point'] = $sum_add_point;
            $organ_record['add_points'] = $add_points;
            $point_data[] = $organ_record;

            $sum_amount += intval($sum_point * $rate);
        }

        $results['is_load'] = true;
        $results['points'] = $point_data;
        $results['all_amount'] = $sum_amount;

        echo json_encode($results);
    }

    private function getAllAttendTime($attends, $from_time, $to_time){
        if (empty($attends)) return 0;
        $sum = 0;
        foreach ($attends as $attend){
            $cur_from = $attend['from_time'] < $from_time ? $from_time : $attend['from_time'];
            $cur_to = (empty($attend['to_time'])) ? date('Y-m-d H:i:s') : $attend['to_time'];
            $cur_to = ($cur_to > $to_time) ? $to_time : $cur_to;
			
			if($cur_to<$cur_from) continue;

				
            $origin = new DateTime($cur_from);
            $target = new DateTime($cur_to);
            $interval = $target->getTimestamp() - $origin->getTimestamp();
            $sum += intval($interval/60 );
        }
        return $sum;
    }

    private function getAllReserveTime($reserves, $from_time, $to_time){
        if (empty($reserves)) return 0;
        $sum = 0;
        foreach ($reserves as $reserve){
            $cur_from = $reserve['from_time'] < $from_time ? $from_time : $reserve['from_time'];
            $cur_to = ($reserve['to_time'] > $to_time) ? $to_time : $reserve['to_time'];

            $origin = new DateTime($cur_from);
            $target = new DateTime($cur_to);
            $interval = $target->getTimestamp() - $origin->getTimestamp();
            $sum += intval($interval/60 );
        }
        return $sum;
    }

    private function getStaffWorkedYear($entering_date, $to_time){
        if (empty($entering_date)) return 0;

        $origin = new DateTime($entering_date);
        $target = new DateTime($to_time);
        if($target<$origin) return 0;

        $date_diff = date_diff($target, $origin);
        return $date_diff->format('%y');
    }

    private function getAttendDays($attends, $from_time, $to_time){
        if (empty($attends)) return 0;
        $attend = $attends[0];

        $days = 0;
        foreach ($attends as $attend){
            $_start = ($attend['from_time'] > $from_time) ? $attend['from_time'] : $from_time;
            $_end = ($attend['to_time'] < $to_time) ? $attend['to_time'] : $to_time;
            $start_datetime = new DateTime($_start);
            $end_datetime = new DateTime($_end);
            if (!empty($cur_datetime) && $start_datetime<=$cur_datetime) {

            }else{
                $cur_datetime = $start_datetime;
            }
            while ($cur_datetime->format('Y-m-d')<=$end_datetime->format('Y-m-d')){
                $days++;
                $cur_datetime->add(new DateInterval('P1D'));
            }
        }
        return $days;
    }

}
?>
