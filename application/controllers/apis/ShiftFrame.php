<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class ShiftFrame extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('shift_frame_model');
        $this->load->model('organ_shift_time_model');
        $this->load->model('shift_frame_group_model');
        $this->load->model('order_model');
        $this->load->model('group_user_model');
        $this->load->model('shift_frame_ticket_model');
    }

    public function list(){
        $organ_id = $this->input->post('organ_id');
        $from_date  = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');

        $dt_from = new DateTime($from_date);
        $dt_to = new DateTime($to_date);

        $dt_to->add(new DateInterval('P1D'));
        
        $from = $dt_from->format('Y-m-d 00:00:00');
        $to = $dt_to->format('Y-m-d 00:00:00');
        
        $cond = array();
        $cond['organ_id'] = $organ_id;
        $cond['from_date'] = $from;
        $cond['to_date'] = $to;

        $data = $this->shift_frame_model->getListData($cond);

        $results['is_load'] = true;
        $results['data'] = $data;

        echo(json_encode($results));
    }

    public function loadShiftFrame(){
        $organ_id = $this->input->post('organ_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');

        $results = [];

        $cond = array();
        $cond['organ_id'] = $organ_id;
        $cond['from_time'] = $from_time;
        $cond['to_time'] = $to_time;

        $shift_frames = $this->shift_frame_model->getListData($cond);

        $results['is_result'] = true;
        $results['data'] = $shift_frames;

        echo(json_encode($results));
    }

    public function loadFrameGroups(){
        $shift_frame_id = $this->input->post('shift_frame_id');
        
        if(empty($shift_frame_id)){
            $results['is_result'] = false;
            $results['err_message'] = 'shift_frame_id is required';
            echo json_encode($results);
            exit;
        }

        $groups = $this->shift_frame_group_model->getDataByParam(['shift_frame_id' => $shift_frame_id]);
        $data = [];
        foreach ($groups as $group){
            $data[] = $group['group_id'];
        }

        $results['is_result'] = true;
        $results['data'] = $data;

        echo(json_encode($results));
    }

    public function saveShiftFrame(){
        $shift_frame_id = $this->input->post('shift_frame_id');
        $organ_id = $this->input->post('organ_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $count = $this->input->post('count');
        $comment = $this->input->post('comment');
        $groups = $this->input->post('groups');
        $staff_id = $this->input->post('staff_id');
        

        $time = strtotime($from_time);
        $weekday = date('w',$time);
        $shift_times = $this->organ_shift_time_model->getListByCond(['organ_id'=>$organ_id, 'weekday'=>$weekday]);
        $isactive = $this->isActiveTime($shift_times, date('H:i:s', strtotime($from_time)), date('H:i:s', strtotime($to_time)));
        if (!$isactive){
            $results['is_result'] = false;
            $results['err'] = 'active_err';
            $results['err_message'] = 'shift frame is out of active bussiness';
            echo json_encode($results);
            return;
        }

        $cond = [];
        $cond['organ_id'] = $organ_id;
        $cond['input_from_time'] = $from_time;
        $cond['input_to_time'] = $to_time;

        $frames = $this->shift_frame_model->getListData($cond);

        $is_duplicatie = false;
        foreach($frames as $item){
            if(empty($shift_frame_id) || ($shift_frame_id != $item['id'])){
                $is_duplicatie = true;
                break;
            }
        }
        if ($is_duplicatie){
            $results['is_result'] = false;
            $results['err'] = 'duplicate_err';
            $results['err_message'] = 'shift_frame is duplicate';
            echo json_encode($results);
            return;
        }

        if (empty($shift_frame_id)){
            $shift = array(
                'organ_id' => $organ_id,
                'from_time' => $from_time,
                'to_time' => $to_time,
                'count' => $count,
				'staff_id' => empty($staff_id) ? null : $staff_id,
                'comment' => $comment
            );
            $shift_frame_id = $this->shift_frame_model->insertRecord($shift);
        }else{
            $shift = $this->shift_frame_model->getFromId($shift_frame_id);
            $shift['from_time'] = $from_time;
            $shift['to_time'] = $to_time;
            $shift['count'] = $count;
            $shift['comment'] = $comment;
			$shift['staff_id'] = empty($staff_id) ? null : $staff_id;
            $this->shift_frame_model->updateRecord($shift, 'id');
        }

        if (!(empty($groups)) && !empty($shift_frame_id)){
            $activeGroups = json_decode($groups);
			$old_groups = $this->shift_frame_group_model->getGroupLists(['shift_frame_id' => $shift_frame_id]);
            $this->shift_frame_group_model->removeGroup(['shift_frame_id' => $shift_frame_id, 'not_group_ids' => $activeGroups]);
			foreach($old_groups as $item){
				if (!in_array($item['group_id'], $activeGroups)) {
					$group_users = $this->group_user_model->getUsers(['group_id'=>$item['group_id']]);
					foreach($group_users as $u){
						$this->order_model->deleteOrder(['organ_id'=> $organ_id, 'user_id' => $u['user_id'], 'shift_frame_id' => $shift_frame_id]);
					}
				}
			}


            foreach($activeGroups as $group){
                $old = $this->shift_frame_group_model->getDataByParam(['shift_frame_id' => $shift_frame_id, 'group_id'=>$group]);
                if (empty($old)){
                    $this->shift_frame_group_model->insertRecord([
                        'shift_frame_id' => $shift_frame_id,
                        'group_id' => $group
                    ]);
                }
				$group_users = $this->group_user_model->getUsers(['group_id'=>$group]);
				foreach($group_users as $u){
					$order = $this->order_model->getOneByParam(['user_id'=>$u['user_id'], 'shift_frame_id'=>$shift_frame_id]);
					
					if (empty($order)) {

							$order = array(
									'organ_id' => $organ_id,
									'table_position' => 1,
									'user_id' => $u['user_id'],
									'shift_frame_id' => $shift_frame_id,
									'from_time' => $from_time,
									'to_time' => $to_time,
									'select_staff_id' => $staff_id,
									'status' => ORDER_STATUS_TABLE_START,
									'is_group_start' => 1,
								);
							$this->order_model->insertRecord($order);
					}else{
						$udata = $order;
						$udata['from_time'] = $from_time;
						$udata['to_time'] = $to_time;
						$udata['select_staff_id'] = $staff_id;
						$this->order_model->updateRecord($udata, 'id');
					}
				}
            }
        }
		$json_tickets = $this->input->post('tickets');
		$tickets = json_decode($json_tickets, true);
		foreach($tickets as $ticket){
			$old = $this->shift_frame_ticket_model->getRowByCond(['shift_frame_id' => $shift_frame_id, 'ticket_id'=>$ticket['ticket_id']]);
			if ($ticket['count']==0) {
				if (!empty($old)) {
					$this->shift_frame_ticket_model->delete_force($old['id'], 'id');
				}
			}else{
				if (empty($old)) {
					$this->shift_frame_ticket_model->insertRecord([
						'shift_frame_id' => $shift_frame_id,
						'ticket_id' => $ticket['ticket_id'],
						'count' => $ticket['count'],
					]);
				}else{
					$old['count'] = $ticket['count'];
					$this->shift_frame_ticket_model->updateRecord($old);
				}
			}
		}

        $results['is_result'] = true;
        echo json_encode($results);
        return;
    }

    public function deleteShiftFrame(){
        $shift_frame_id = $this->input->post('shift_frame_id');
        if (empty($shift_frame_id)){
            $results['is_result'] = false;
            $results['err_message'] = 'shift_frame_id is required';
            echo json_encode($results);
        }

        $this->shift_frame_model->delete_force($shift_frame_id, 'id');
        $this->shift_frame_group_model->removeGroup(['shift_frame_id' => $shift_frame_id]);
		
		$this->order_model->deleteOrder(['shift_frame_id' => $shift_frame_id]);
		$this->shift_frame_ticket_model->delete_force($shift_frame_id, 'shift_frame_id');
        
        $results['is_result'] = true;
        echo json_encode($results);
    }

    public function loadActiveShiftFrames(){
        $organ_id = $this->input->post('organ_id');
        $selected_time = $this->input->post('selected_time');
        if (empty($selected_time) || empty($organ_id)){
            $results['is_result'] = false;
            $results['err_message'] = 'organ_id, selected_time are require parameter';
            echo json_encode($results);
            exit;
        }

        $shift_frames = $this->shift_frame_model->getListData([
            'organ_id' => $organ_id, 
            'input_from_time' => $selected_time, 
            'input_to_time' => $selected_time
        ]);

        if (empty($shift_frames)){
            $results['is_result'] = false;
            $results['err_message'] = 'empty shift frames';
            echo json_encode($results);
            exit;
        }
        $results['is_result'] = true;
        $results['data'] = $shift_frames;

        echo json_encode($results);
    }

    public function copyShiftFrames(){
        $organ_id = $this->input->post('organ_id');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');

        $from_time = $from_date." 00:00:00";
        $to_time = $to_date ." 23:59:59";

        $olds = $this->shift_frame_model->getListData(['organ_id'=>$organ_id, 'from_time'=>$from_time, 'to_time' =>$to_time]);
        if (!empty($olds)){
            $this->shift_frame_model->removeShiftFrame(['organ_id'=>$organ_id, 'from_time'=>$from_time, 'to_time' =>$to_time]);
            $this->shift_frame_group_model->removeGroup(['shift_frame_ids' => array_column($olds, 'id')]);
			$this->order_model->deleteOrder(['shift_frame_ids' => array_column($olds, 'id')]);
			foreach($olds as $item){
				$this->shift_frame_ticket_model->delete_force($item['id'], 'shift_frame_id');
			}
        }

        $from = new DateTime($from_time);
        $to = new DateTime($to_time);
        $diffDay = new DateInterval('P7D');

        $shift_frames = [];
        $cnt = 0;
        while(empty($shift_frames) && $from->format('Y') >= '2021'){
            $cnt++;
            $from->sub($diffDay);
            $to->sub($diffDay);
            $shift_frames = $this->shift_frame_model->getListData(['organ_id'=>$organ_id, 'from_time'=>$from->format("Y-m-d H:i:s"), 'to_time' =>$to->format("Y-m-d H:i:s")]);
        }

        // $copyFrom = $from->format('Y-m-d H:i:s');
        // $copyTo = $to->format('Y-m-d H:i:s');
        $diffDay = new DateInterval('P'.(7*$cnt).'D');
        

        foreach ($shift_frames as $item){
            $copyFrom = new DateTime($item['from_time']);
            $copyTo = new DateTime($item['to_time']);
            $copy_data = array(
                'organ_id' => $organ_id,
                'from_time'=> $copyFrom->add($diffDay)->format('Y-m-d H:i:s'),
                'to_time'=> $copyTo->add($diffDay)->format('Y-m-d H:i:s'),
                'count' => $item['count'],
                'comment' => $item['comment']
            );

            $insert_id = $this->shift_frame_model->insertRecord($copy_data);

            if (!empty($insert_id)){
                $groups = $this->shift_frame_group_model->getDataByParam(['shift_frame_id' => $item['id']]);
                if (!empty($groups)){
                    foreach ($groups as $group){
                        $insertGroup = [
                            'shift_frame_id' => $insert_id,
                            'group_id' => $group['group_id'],
                        ];
                        $this->shift_frame_group_model->insertRecord($insertGroup);

		                $group_users = $this->group_user_model->getUsers(['group_id'=>$group['group_id']]);

						foreach($group_users as $user){
							$order = array(
									'organ_id' => $organ_id,
									'table_position' => 1,
									'user_id' => $user['user_id'],
									'shift_frame_id' => $insert_id,
									'from_time' => $copy_data['from_time'],
									'to_time' => $copy_data['to_time'],
									'select_staff_id' => $item['staff_id'],
									'status' => ORDER_STATUS_TABLE_START,
									'is_group_start' => 1
								);
							$this->order_model->insertRecord($order);
						}
                    }
                }

				$frame_tickets = $this->shift_frame_ticket_model->getListData(['shift_frame_id' => $item['id']]);
				foreach($frame_tickets as $origin){
					$this->shift_frame_ticket_model->insertRecord([
							'shift_frame_id'=>$insert_id,
							'ticket_id'=>$origin['ticket_id'],
							'count' => $origin['count']
						]);
				}
            }
        }

        $results = [];
        $results['is_result'] = true;

        echo json_encode($results);


    }

    public function loadShiftFrameOfReserve(){
        $organ_id = $this->input->post('organ_id');
        $from_time = $this->input->post('from_time');
        $to_time = $this->input->post('to_time');
        $user_id = $this->input->post('user_id');

        $results = [];

        $cond = array();
        $cond['organ_id'] = $organ_id;
        $cond['from_time'] = $from_time;
        $cond['to_time'] = $to_time;

        $shift_frames = $this->shift_frame_model->getListData($cond);

        $data = [];
        foreach($shift_frames as $item){
            
            $group = $this->shift_frame_group_model->getOneByParam([
                'shift_frame_id' => $item['id']
            ]);
            $group_users = [];
            if (!empty($group)){
                $group_users = $this->group_user_model->getUsers(['group_id'=>$group['group_id']]);
            }

            $tmp = $item;
            $tmp['is_reserve'] = false;
            $orders = $this->order_model->getListData([
                'organ_id'=>$organ_id, 
                'from_time'=>$item['from_time'], 
                'to_time'=>$item['to_time'], 
                'is_not_users' => implode(',', array_column($group_users, 'user_id'))
            ]);
            if (in_array($user_id, array_column($orders, 'user_id'))){
                $tmp['is_reserve'] = true;
            }

            $cnt = empty($orders) ? 0 : count($orders);
            $total = empty($item['count']) ? 0 : $item['count'];
            $tmp['blank_cnt'] = $total - $cnt;

            $data[] = $tmp;
        }


        $results['is_result'] = true;
        $results['data'] = $data;

        echo(json_encode($results));
    }

    private function isActiveTime($shift_times, $from_time, $to_time){
        $isActive = false;
        foreach ($shift_times as $record){
            $_start = $record['from_time'];
            $_end = $record['to_time'];
            if ($_start.":00"<=$from_time && $_end.":00">=$to_time){
                $isActive = true;
                break;
            }
        }
        return $isActive;
    }

}
?>
