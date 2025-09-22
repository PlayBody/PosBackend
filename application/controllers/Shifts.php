<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 * Shift Controller
 */

class Shifts extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('shift_model');
        $this->load->model('shift_frame_model');
        $this->load->model('shifts/shift_rest_model');
        $this->load->model('setting_init_shift_model');
        $this->load->model('shift_meta_model');
    }

    public function forceSaveShift(){
        $shift_id = $this->input->post('shift_id');
        $organ_id = $this->input->post('organ_id');
        $staff_id = $this->input->post('staff_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $shift_type = $this->input->post('shift_type');
        $deleted = $this->input->post('deleted');
        $ref_shift_id = $this->input->post('ref_shift_id');

        // echo json_encode(['shift_id'=>$shift_id, 'organ_id'=>$organ_id]);
        // return;

        if(empty($staff_id)){
            echo "[]";
        } else {
            if($shift_id == '' || $shift_id < 1){
                $shift = array(
                    'organ_id' => $organ_id,
                    'staff_id' => $staff_id,
                    'from_time' => $from_time,
                    'to_time' => $to_time,
                    'shift_type' => $shift_type,
                    'visible' => 1,
                );
                $shift_id = $this->shift_model->insertRecord($shift);
            } else {
                if($deleted == '1'){
                    $this->shift_model->delete_force($shift_id, 'shift_id');
                    if($ref_shift_id != '0'){
                        $oldShift = $this->shift_model->getFromId($ref_shift_id);
                        if($oldShift['shift_type'] == SHIFT_STATUS_APPLY ||
                            $oldShift['shift_type'] == SHIFT_STATUS_ME_APPLY ||
                            $oldShift['shift_type'] == SHIFT_STATUS_SUBMIT ){
                            if($from_time < $to_time){
                                $this->shift_meta_model->saveData($ref_shift_id, $from_time, $to_time);
                            }
                        }
                    }
                } else {
                    $oldShift = $this->shift_model->getFromId($shift_id);
                    if($shift_type == SHIFT_STATUS_REQUEST){
                        if($oldShift['shift_type'] == SHIFT_STATUS_APPLY ||
                            $oldShift['shift_type'] == SHIFT_STATUS_ME_APPLY ||
                            $oldShift['shift_type'] == SHIFT_STATUS_SUBMIT ){
                            
                            $ost = $oldShift['from_time'].".000";
                            $oen = $oldShift['to_time'].".000";
    
                            if($ost > $from_time || $oen < $to_time){
                                $st = max($ost, $from_time);
                                $en = min($oen, $to_time);
                                if($st < $en){
                                    $this->shift_meta_model->saveData($shift_id, $st, $en);
                                }
                            }
                        }
                    }
    
                    $shift = array(
                        'shift_id' => $shift_id,
                        'organ_id' => $organ_id,
                        'staff_id' => $staff_id,
                        'from_time' => $from_time,
                        'to_time' => $to_time,
                        'shift_type' => $shift_type,
                    );
    
                    $this->shift_model->updateRecord($shift, 'shift_id');
                }
            }
            
            $result['shift_id'] = $shift_id;
            echo json_encode($result);
        }
    }


    public function saveStaffInput(){
        $shift_id = $this->input->post('shift_id');
        $organ_id = $this->input->post('organ_id');
        $staff_id = $this->input->post('staff_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $shift_type = $this->input->post('shift_type');

        if ($shift_type==SHIFT_STATUS_ME_REJECT){
            $shift = $this->shift_model->getFromId($shift_id);
            if (empty($shift)){
                $results['isSave'] = false;
                echo json_encode($results);
                return;
            }
            $shift_meta = $this->shift_meta_model->getDataByParam(["shift_id"=>$shift_id]);//$shift_id, 'shift_id');
            if(empty($shift_meta)){
                if (empty($shift['old_shift'])){
                    $this->shift_model->delete_force($shift_id, 'shift_id');
                }else{
                    $shift['shift_type'] = $shift['old_shift'];
                    $shift['old_shift'] = null;
                    $this->shift_model->updateRecord($shift, 'shift_id');
    
                    $this->mergeShift($shift_id, $staff_id, $organ_id, $from_time, $to_time, $shift['shift_type']);
                }
                $results['isSave'] = true;
                echo json_encode($results);
                return;
            } else {
                $shift['shift_type'] = SHIFT_STATUS_APPLY;
                $shift['old_shift'] = null;
                $this->shift_model->delete_force($shift_id, 'shift_id');
                for($i = 0; $i < count($shift_meta); $i++){
                    $meta = $shift_meta[$i];
                    $shift['from_time'] = $meta['from_time'];
                    $shift['to_time'] = $meta['to_time'];
                    if (isset($shift['shift_id'])) {
                        unset($shift['shift_id']);
                    }
                    $this->shift_model->insertRecord($shift);
                    $this->shift_meta_model->delete_force($meta['meta_id'], 'meta_id');
                }
                $results['isSave'] = true;
                echo json_encode($results);
                return;
            }

        }
//        if ($shift_type==SHIFT_STATUS_ME_REPLY){
//            $shift = $this->shift_model->getFromId($shift_id);
//
//            $shift['shift_type'] = $shift_type;
//            $this->shift_model->updateRecord($shift, 'shift_id');
//
//            $results['isSave'] = true;
//            echo json_encode($results);
//            return;
//        }

        if($shift_type==SHIFT_STATUS_REST){
            $shifts = $this->shift_model->getListByCond([
                'staff_id'=>$staff_id,
                'organ_id' => $organ_id,
                'select_date' => substr($from_time, 0,10),
            ]);

            if(!empty($shifts)){
                $results['isSave'] = false;
                $results['message'] = 'シフトが存在します。 休みを設定するには、シフトを削除してください。';
                echo json_encode($results);
                return;
            }

            $shift = array(
                'staff_id' => $staff_id,
                'organ_id' => $organ_id,
                'from_time' => substr($from_time, 0,10). ' 00:00:00',
                'to_time' => substr($from_time, 0,10). ' 23:59:59',
                'shift_type' => $shift_type,
                'visible' => 1,
            );
            $this->shift_model->insertRecord($shift);
            $results['isSave'] = true;
            echo json_encode($results);
            return;

        }else{
            $this->shift_model->deleteDayShift(substr($from_time, 0,10), $staff_id, $organ_id, SHIFT_STATUS_REST);
        }

        $exist_other_shifts = $this->shift_model->getListByCond([
            'staff_id'=>$staff_id,
            'organ_id' => $organ_id,
            'in_from_time' => $from_time,
            'in_to_time' => $to_time,
            'no_shift' => $shift_id,
        ]);

        if (!empty($exist_other_shifts)){
            $results['isSave'] = false;
            $results['message'] = '入力したシフトが重複しました。時間を確認してください。';
            echo json_encode($results);
            return;
        }

        // $isReadyCount = $this->isInCount($organ_id, $from_time, $to_time);
        $inner_counts = $this->shift_frame_model->getListByCond([
            'organ_id'=>$organ_id,
            'in_from_time'=>$from_time,
            'in_to_time'=>$to_time,
        ]);

        if(empty($inner_counts)) {
            $results['isSave'] = false;
            $results['message'] = '勤務計画が準備されていません。';
            echo json_encode($results);
            return;
        }

        $link_counts = [];
        $last = [];
        if (count($inner_counts)==1){
            $link_counts = $inner_counts;
        } else{
            foreach ($inner_counts as $count){
                if (empty($last)){
                    $last = $count;
                }else{
                    if ($last['to_time']==$count['from_time']){
                        $last['to_time'] = $count['to_time'];
                    }else{
                        $link_counts[] = $last;
                        $last = $count;
                    }
                }
            }
            $link_counts[] = $last;
        }

        foreach ($link_counts as $count){
            $_from  = $from_time>=$count['from_time'] ? $from_time : $count['from_time'];
            $_to  = $to_time<=$count['to_time'] ? $to_time : $count['to_time'];
            if (empty($shift_id)){
                $shift = array(
                    'organ_id' => $organ_id,
                    'staff_id' => $staff_id,
                    'from_time' => $_from,
                    'to_time' => $_to,
                    'shift_type' => $shift_type,
                    'visible' => 1,
                );

                $prev_shift = $this->shift_model->getOneByParam([
                    'staff_id' => $staff_id,
                    'organ_id' => $organ_id,
                    'to_time' => $_from,
                    'shift_type' => $shift_type
                ]);

                $next_shift = $this->shift_model->getOneByParam([
                    'staff_id' => $staff_id,
                    'organ_id' => $organ_id,
                    'from_time' => $_to,
                    'shift_type' => $shift_type
                ]);

                if (!empty($prev_shift)){
                    $prev_shift['to_time'] = $_to;
                    $this->shift_model->updateRecord($prev_shift, 'shift_id');
                    $shift_id = $prev_shift['shift_id'];
                    $from_time = $prev_shift['from_time'];
                }elseif (!empty($next_shift)){
                    $next_shift['from_time'] = $_from;
                    $this->shift_model->updateRecord($next_shift, 'shift_id');
                    $shift_id = $next_shift['shift_id'];
                    $to_time = $next_shift['to_time'];
                }else{
                    $shift_id = $this->shift_model->insertRecord($shift);
                }
            }else{
                $shift = $this->shift_model->getFromId($shift_id);
                $shift['from_time'] = $_from;
                $shift['to_time'] = $_to;
                $shift['shift_type'] = $shift_type;

                $this->shift_model->updateRecord($shift, 'shift_id');
            }

        }


        $this->mergeShift($shift_id, $staff_id, $organ_id, $from_time, $to_time, $shift_type);


        $result['isSave'] = true;
        echo json_encode($result);
    }

    /*
     * Merge with old shift.
     *
     */
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

    private function isInCount($organ_id, $from_time, $to_time){
        $counts = $this->shift_frame_model->getListByCond([
            'organ_id'=>$organ_id,
            'in_from_time'=>$from_time,
            'in_to_time'=>$to_time,
        ]);
        if (empty($counts)) return false;
        $link_counts = [];
        $last = [];
        if (count($counts)==1){
            $link_counts = $counts;
        } else{
            foreach ($counts as $count){
                if (empty($last)){
                    $last = $count;
                }else{
                    if ($last['to_time']==$count['from_time']){
                        $last['to_time'] = $count['to_time'];
                    }else{
                        $link_counts[] = $last;
                        $last = $count;
                    }
                }
            }
            $link_counts[] = $last;
        }

        foreach ($link_counts as $count){
            if ($count['from_time']<$to_time && $count['to_time']>$from_time) return true;
        }


        return false;
    }

    
    public function applyInitShift(){
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $organ_id = $this->input->post('organ_id');
        $staff_id = $this->input->post('staff_id');
        $pattern = $this->input->post('pattern');

        $from_time = $from_date . ' 00:00:00';
        $to_time = $to_date . ' 23:59:59';

        $counts = $this->shift_frame_model->getListByCond([
            'organ_id'=>$organ_id,
            'from_time'=>$from_time,
            'to_time'=>$to_time,
        ]);

        if (empty($counts)){
            $results['isSave'] = false;
            $results['message'] = '勤務計画が準備されていません。';
            echo json_encode($results);
            return;
        }

        $apply_shifts = $this->shift_model->getListByCond([
            'staff_id'=>$staff_id,
            'organ_id' => $organ_id,
            'from_time' => $from_time,
            'to_time' => $to_time,
            'no_edit' => 1
        ]);

        if (!empty($apply_shifts)){
            $results['isSave'] = false;
            $results['message'] = '確定したシフトが存在するため、初期設定を適用できません。';
            echo json_encode($results);
            return;
        }

        $pattern = empty($pattern) ? 1 : $pattern;

        $shifts = $this->shift_model->getListByCond([
            'staff_id'=>$staff_id,
            'organ_id' => $organ_id,
            'from_time' => $from_time,
            'to_time' => $to_time,
        ]);

        foreach ($shifts as $shift) {
            $this->shift_model->delete_force($shift['shift_id'], 'shift_id');
        }

        $condInit['staff_id'] = $staff_id;
        $condInit['organ_id'] = $organ_id;
        $condInit['pattern'] = $pattern;
        $initData = $this->setting_init_shift_model->getListByCond([
            'staff_id' => $staff_id,
            'organ_id' => $organ_id,
            'pattern' => $pattern
        ]);

        foreach ($initData as $item){
            $diff1Day = new DateInterval('P'.($item['weekday']-1).'D');
            $curDateTime = new DateTime($from_time);
            $curDateTime->add($diff1Day);
            $sel_date = $curDateTime->format("Y-m-d");

            if($item['shift_type']==SHIFT_STATUS_REST){
                $add_shift = array(
                    'from_time'=> $sel_date . ' 00:00:00',// $input_from,
                    'to_time' => $sel_date . ' 23:59:59', //$input_to,
                    'staff_id' => $staff_id,
                    'organ_id' => $organ_id,
                    'visible' => 1,
                    'shift_type' => empty($item['shift_type']) ? SHIFT_STATUS_SUBMIT : $item['shift_type'],
                );
                $this->shift_model->insertRecord($add_shift);


                continue;
            }

            $item_from = $sel_date . ' ' . $item['from_time'];
            $item_to = $sel_date . ' ' . $item['to_time'];

            $inner_counts = $this->shift_frame_model->getListByCond([
                'organ_id'=>$organ_id,
                'in_from_time'=>$item_from,
                'in_to_time'=>$item_to,
            ]);

            if(empty($inner_counts)) continue;

            $link_counts = [];
            $last = [];
            if (count($inner_counts)==1){
                $link_counts = $inner_counts;
            } else{
                foreach ($inner_counts as $count){
                    if (empty($last)){
                        $last = $count;
                    }else{
                        if ($last['to_time']==$count['from_time']){
                            $last['to_time'] = $count['to_time'];
                        }else{
                            $link_counts[] = $last;
                            $last = $count;
                        }
                    }
                }
                $link_counts[] = $last;
            }

            foreach ($link_counts as $count){
                $_from  = $item_from>=$count['from_time'] ? $item_from : $count['from_time'];
                $_to  = $item_to<=$count['to_time'] ? $item_to : $count['to_time'];
                $add_shift = array(
                    'from_time'=> $_from,// $input_from,
                    'to_time' => $_to, //$input_to,
                    'staff_id' => $staff_id,
                    'organ_id' => $organ_id,
                    'visible' => 1,
                    'shift_type' => empty($item['shift_type']) ? SHIFT_STATUS_SUBMIT : $item['shift_type'],
                );

                $this->shift_model->insertRecord($add_shift);
            }

        }

        $result['isSave'] = true;
        echo json_encode($result);
    }

}
?>
