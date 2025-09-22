<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

class Apipayslips extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('attendance_model');
        $this->load->model('staff_model');
        $this->load->model('staff_point_setting_model');
        $this->load->model('staff_point_add_model');
        $this->load->model('history_table_model');
    }

    public function loadPaySlips(){
        $staff_id = $this->input->post('staff_id');
        $date_month = $this->input->post('date_month');

        $all_time = 0;
        $attendance_date = '';
        $attendance_list = $this->attendance_model->getAttendanceList($staff_id, $date_month);

        $day_times = [];
        if (empty($attendance_list)){
            $all_time = 0;
        }else{
            if ($attendance_list[0]['attendance_status']=='2'){
                $start = $date_month . '-01 00:00:00';
                $start_date = $date_month . '-01';
            }

            foreach ($attendance_list as $item){
                if ($item['attendance_status']=='1'){
                    $start = $item['attendance_time'];
                    $start_date = $item['attendance_date'];
                }else{
                    $end = $item['attendance_time'];
                    $end_date = $item['attendance_date'];

                    $startDateTime = new DateTime($start);
                    $endDateTime = new DateTime($end);

                    $dateDiff = date_diff($startDateTime, $endDateTime);

                    $all_time += $dateDiff->d * 1440 + $dateDiff->h * 60 + $dateDiff->i;

                    if ($start_date == $end_date){
                        if (empty($day_times[substr($start_date, 8)]))
                            $day_times[substr($start_date, 8)] = $dateDiff->d * 1440 + $dateDiff->h * 60 + $dateDiff->i;
                        else
                            $day_times[substr($start_date, 8)] += $dateDiff->d * 1440 + $dateDiff->h * 60 + $dateDiff->i;

                    }else{
                        $cur_date = $start_date;
                        while($cur_date<=$end_date){
//                            var_dump(substr($cur_date, 8));die();
                            if ($cur_date == $start_date){
                                $startDateTime = new DateTime($start);
                                $endDateTime = new DateTime($cur_date . ' 23:59:59');
                                $dateDiff = date_diff($startDateTime, $endDateTime);
                                if (empty($day_times[substr($cur_date, 8)]))
                                    $day_times[substr($cur_date, 8)] = $dateDiff->d * 1440 + $dateDiff->h * 60 + $dateDiff->i;
                                else
                                    $day_times[substr($cur_date, 8)] += $dateDiff->d * 1440 + $dateDiff->h * 60 + $dateDiff->i;
                            }elseif ($cur_date == $end_date){
                                $startDateTime = new DateTime($cur_date. ' 00:00:00');
                                $endDateTime = new DateTime($end);
                                $dateDiff = date_diff($startDateTime, $endDateTime);
                                if (empty($day_times[substr($cur_date, 8)]))
                                    $day_times[substr($cur_date, 8)] = $dateDiff->d * 1440 + $dateDiff->h * 60 + $dateDiff->i;
                                else
                                    $day_times[substr($cur_date, 8)] += $dateDiff->d * 1440 + $dateDiff->h * 60 + $dateDiff->i;
                            }else{
                                $day_times[substr($cur_date, 8)] = 1440;
                            }

                            $diff1Day = new DateInterval('P1D');
                            $curDateTime = new DateTime($cur_date);

                            $curDateTime->add($diff1Day);
                            $cur_date = $curDateTime->format("Y-m-d");
                        }
                    }
                }
            }
        }

        $enable_day = 0;
        foreach ($day_times as $time) {
            if ($time>30) $enable_day++;
        }

        $staff = $this->staff_model->getFromId($staff_id);

        $defualt_amount = (empty($staff['staff_salary_months'])? 0 : $staff['staff_salary_months']) +
            (empty($staff['staff_salary_days'])? 0 : $staff['staff_salary_days']) * $enable_day +
            (empty($staff['staff_salary_times'])? 0 : $staff['staff_salary_times']) * floor($all_time / 60);


        $cond = [];
        $cond['staff_id'] = $staff_id;

        $cond['setting_year'] = preg_split('/-/', $date_month)[0];
        $cond['setting_month'] = preg_split('/-/', $date_month)[1];
        $point_setting = $this->staff_point_setting_model->getSettingData($cond);
        if (empty($point_setting)){
            $point_amount = 0;
        }else{
            $cond = [];
            $cond['point_setting_id'] = $point_setting['id'];
            $point_amount= $this->staff_point_add_model->getPointsSum($cond);
        }


        $first_day = $date_month . '-01 00:00:00';
        $diff1Month = new DateInterval('P1M');

        $curDateTime = new DateTime($first_day);

        $curDateTime->add($diff1Month);
        $last_day = $curDateTime->format("Y-m-d H:i:s");

        $back_amount = $this->history_table_model->getBackAmount($staff_id, $first_day, $last_day);

        $rate = $this->clacPersonRate($staff_id, preg_split('/-/', $date_month)[0], preg_split('/-/', $date_month)[1]);

        $results['all_time'] = $all_time;
        $results['company'] = $staff['company_id'];
        $results['defualt_amount'] = $defualt_amount;
        $results['point_amount'] = $point_amount;
        $results['back_amount'] = $back_amount;
        $results['staff_rate'] = $rate;

        echo json_encode($results);

    }


}
?>