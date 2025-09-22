<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 * Shift Controller
 */

class Managements extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('organ_model');
        $this->load->model('shift_model');
        $this->load->model('notification_text_model');
        $this->load->model('shift_frame_model');
        $this->load->model('shifts/shift_rest_model');
    }

    public function loadInit(){
        $organ_id = $this->input->post('organ_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');

        $counts = $this->shift_frame_model->getListByCond(['organ_id' => $organ_id, 'from_time' => $from_time, 'to_time' => $to_time]);
        $times = [];

        foreach($counts as $count){
            if(!array_search($count['from_time'], $times) && (empty($times) || $times[0]!=$count['from_time'])) $times[] = $count['from_time'];
            if(!array_search($count['to_time'], $times) && (empty($times) || $times[0]!=$count['to_time'])) $times[] = $count['to_time'];
        }

        $shifts = $this->shift_model->getListByCond(['organ_id' => $organ_id, 'from_time' => $from_time, 'to_time' => $to_time]);

        foreach($shifts as $shift){
            if(!array_search($shift['from_time'], $times) && (empty($times) || $times[0]!=$shift['from_time'])) $times[] = $shift['from_time'];
            if(!array_search($shift['to_time'], $times) && (empty($times) || $times[0]!=$shift['to_time'])) $times[] = $shift['to_time'];
        }

        sort($times);

        $data = [];
        for($i=0; $i<count($times)-1; $i++){
            $tmp = [];
            $tmp['from_time'] = $times[$i];
            $tmp['to_time'] = $times[$i+1];
            $_counts = $this->shift_frame_model->getListByCond(['organ_id' => $organ_id, 'in_from_time' => $times[$i], 'in_to_time' => $times[$i+1]]);
            $tmp['count'] = empty($_counts) ? 0 : $_counts[0]['count'];

            $_applies = $this->shift_model->getListByCond(['organ_id' => $organ_id, 'in_from_time' => $times[$i], 'in_to_time' => $times[$i+1], 'is_apply' => '1']);
            $tmp['apply'] = empty($_applies) ? 0 : count($_applies);

            $_shifts = $this->shift_model->getListByCond(['organ_id' => $organ_id, 'in_from_time' => $times[$i], 'in_to_time' => $times[$i+1], 'is_apply_enable' => '1']);
            $tmp['shift'] = empty($_shifts) ? 0 : count($_shifts);

            if(!empty($_shifts))  $tmp['shifts'] = $_shifts;
            if($tmp['count']==0 && $tmp['shift']==0) continue;

            $data[] = $tmp;
        }

        $results['data'] = $data;

        echo json_encode($results);
    }

    public function saveShiftManage(){
        $organ_id = $this->input->post('organ_id');
        $organ = $this->organ_model->getFromId($organ_id);
        $company_id = $organ['company_id'];
        $json = $this->input->post('data');
        $datas = json_decode($json, true);
        foreach ($datas as $data){
            $staff_id = $data['staff_id'];
            $from_time = $data['from_time'];
            $to_time = $data['to_time'];
            $shift_type = $data['shift_type'];

            $this->shift_model->deleteDayShift(substr($from_time, 0,10), $staff_id, $organ_id, SHIFT_STATUS_REST);

            $old_shift = $this->shift_model->getRecordByCond(['staff_id'=>$staff_id, 'organ_id'=>$organ_id, 'in_from_time'=>$from_time, 'in_to_time'=>$to_time], true);
            if (empty($old_shift)){
                $shift = array(
                    'organ_id' => $organ_id,
                    'staff_id' => $staff_id,
                    'from_time' => $from_time,
                    'to_time' => $to_time,
                    'shift_type' => $shift_type,
                    'visible' => 1,
                );
                $shift_id = $this->shift_model->insertRecord($shift);
            }else{
                

                if ($shift_type == SHIFT_STATUS_ME_REJECT){
                    if (empty($old_shift['old_shift'])){
                        $this->shift_model->delete_force($old_shift['shift_id'], 'shift_id');
                        continue;
                    }else{
                        $old_shift['shift_type'] = $old_shift['old_shift'];
                        $old_shift['old_shift'] = null;

                        $this->shift_model->updateRecord($old_shift, 'shift_id');
                    }
                    $this->mergeShift($old_shift['shift_id'], $old_shift['staff_id'], $old_shift['organ_id'], $old_shift['from_time'], $old_shift['to_time'], $old_shift['shift_type']);
                    continue;
                }

                $old_type = $old_shift['shift_type'] == SHIFT_STATUS_REQUEST ? $old_shift['old_shift'] : $old_shift['shift_type'];
                if ($old_shift['from_time']==$from_time && $old_shift['to_time']==$to_time){
                    $old_shift['shift_type'] = $shift_type;
                    $old_shift['old_shift'] = $old_type;
                    $this->shift_model->updateRecord($old_shift, 'shift_id');
                    $shift_id = $old_shift['shift_id'];
                }else if($old_shift['from_time']==$from_time || $old_shift['to_time']==$to_time) {
                    if($old_shift['from_time']==$from_time){
                        $old_shift['from_time'] = $to_time;
                    }
                    if($old_shift['to_time']==$to_time){
                        $old_shift['to_time'] = $from_time;
                    }

                    $this->shift_model->updateRecord($old_shift, 'shift_id');

                    $shift = array(
                        'staff_id' => $staff_id,
                        'organ_id' => $organ_id,
                        'from_time' => $from_time,
                        'to_time' => $to_time,
                        'shift_type' => $shift_type,
                        'old_shift' => $old_type,
                        'visible' => 1
                    );
                    $shift_id = $this->shift_model->insertRecord($shift);
                }else{
                    $tmp = $old_shift;
                    $old_shift['to_time'] = $from_time;
                    $this->shift_model->updateRecord($old_shift, 'shift_id');

                    $shift = array(
                        'staff_id' => $staff_id,
                        'organ_id' => $organ_id,
                        'from_time' => $from_time,
                        'to_time' => $to_time,
                        'shift_type' => $shift_type,
                        'old_shift' => $old_type,
                        'visible' => 1
                    );
                    $shift_id = $this->shift_model->insertRecord($shift);

                    $shift = array(
                        'staff_id' => $staff_id,
                        'organ_id' => $organ_id,
                        'from_time' => $to_time,
                        'to_time' => $tmp['to_time'],
                        'shift_type' => $tmp['shift_type'],
                        'old_shift' => $tmp['old_shift'],
                        'visible' => 1
                    );
                    $this->shift_model->insertRecord($shift);
                }
            }

            if ($shift_type != SHIFT_STATUS_REQUEST && $shift_type != SHIFT_STATUS_ME_REPLY){
                $this->mergeShift($shift_id, $staff_id, $organ_id, $from_time, $to_time, $shift_type);
            }

//            $notify_type = '';
//            //if($shift_type == SHIFT_STATUS_APPLY)
//            //    $notify_type = '17';
//            //if($shift_type == SHIFT_STATUS_REJECT)
//            //    $notify_type = '18';
//            if($shift_type == SHIFT_STATUS_REQUEST)
//                $notify_type = '19';
//            if (!empty($notify_type)){
//                //$text_data = $this->notification_text_model->getRecordByCond(['company_id'=>$company_id, 'mail_type'=> $notify_type]);
//
////                $title = empty($text_data['title']) ? 'タイトルなし' : $text_data['title'];
////                $content = empty($text_data['content']) ? 'タイトルなし' : $text_data['content'];
////                $content = str_replace('$select_month', substr($from_time, 5,2), $content);
////                $content = str_replace('$select_day', substr($from_time, 8,2), $content);
////                $content = str_replace('$from_time', substr($from_time, 11,5), $content);
////                $content = str_replace('$to_time', substr($to_time, 11,5), $content);
////                $is_fcm = $this->sendNotifications($notify_type, $title, $content, '', $staff_id, '1');
//            }
        }

        $this->shift_model->delete_force('0', 'shift_type');

        echo json_encode(['isUpdate' => true]);
    }

    private function mergeShift($shift_id, $staff_id, $organ_id, $from_time, $to_time, $shift_type){
        $prev_shift = $this->shift_model->getOneByParam([
            'staff_id' => $staff_id,
            'organ_id' => $organ_id,
            'to_time' => $from_time,
            'shift_type' => $shift_type
        ]);

        if (!empty($prev_shift)){
            $prev_shift['to_time'] = $to_time;
            $this->shift_model->updateRecord($prev_shift, 'shift_id');
            $this->shift_model->delete_force($shift_id, 'shift_id');

            $from_time = $prev_shift['from_time'];
            $shift_id = $prev_shift['shift_id'];
        }

        $next_shift = $this->shift_model->getOneByParam([
            'staff_id' => $staff_id,
            'organ_id' => $organ_id,
            'from_time' => $to_time,
            'shift_type' => $shift_type
        ]);

        if (!empty($next_shift)){
            $next_shift['from_time'] = $from_time;
            $this->shift_model->updateRecord($next_shift, 'shift_id');
            $this->shift_model->delete_force($shift_id, 'shift_id');
        }

        return true;
    }
}
?>
