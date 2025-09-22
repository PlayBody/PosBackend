<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Orders extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('order_model');
        $this->load->model('shift_frame_ticket_model');
        $this->load->model('user_ticket_model');
        $this->load->model('shift_frame_group_model');
        $this->load->model('group_model');
        $this->load->model('user_model');
		$this->load->model('staff_organ_model');
    }

	public function enteringShiftFrameOrderCheck()
	{
		$now = date('Y-m-d H:i:00');

		$orders = $this->order_model->getListData([
				'equal_from_time' => $now,
				'is_group_start' => 1,
				'status' => ORDER_STATUS_TABLE_START
			]);

		
		foreach ($orders as $order) {
			$shift_frame_id = $order['shift_frame_id'];
			$user_id = $order['user_id'];

			$staff_id = $order['select_staff_id'];
			if (empty($shift_frame_id) || empty($user_id)) continue;

			$frameTickets = $this->shift_frame_ticket_model->getListData(['shift_frame_id' => $shift_frame_id]);

			$isOutTicket = false;
			
			foreach($frameTickets as $item){
				$uTicket = $this->user_ticket_model->getUserTicket(['user_id' => $user_id, 'ticket_id' => $item['ticket_id']]);
				if (empty($uTicket)) {
					$isOutTicket = true;
				}else{
					if (empty($uTicket['count']) || $uTicket['count'] < $item['count']){
						$isOutTicket = true;
					}
				}
			}

			if ($isOutTicket && !empty($staff_id)) {
				$staff = $this->staff_model->getFromId($staff_id);
				if (!empty($staff['staff_mail'])) {
					$group_name = $this->getGroupName($shift_frame_id);
					$user = $this->user_model->getFromId($user_id);
					$title = "チケット不足のお客様の出席";
					$body = "グループ".$group_name."にて、".$user['user_nick']."さんのチケットが不足した状態で出席状態を迎えました。";

					$this->sendMailMessage($title, $body, $staff['staff_mail']);
				}
			}
		}
	}

    public function completeShiftFrameOrder(){
		$now = date('Y-m-d H:i:00');

		$orders = $this->order_model->getListData([
				'equal_to_time' => $now,
				'is_group_start' => 1,
				'status' => ORDER_STATUS_TABLE_START
			]);


		foreach ($orders as $order) {
			$shift_frame_id = $order['shift_frame_id'];
			$user_id = $order['user_id'];
			if (empty($shift_frame_id) || empty($user_id)) continue;

			$frameTickets = $this->shift_frame_ticket_model->getListData(['shift_frame_id' => $shift_frame_id]);

			$isOutTicket = false;
			
			foreach($frameTickets as $item){
				$uTicket = $this->user_ticket_model->getUserTicket(['user_id' => $user_id, 'ticket_id' => $item['ticket_id']]);
				if (empty($uTicket)) {
					$isOutTicket = true;
				}else{
					if (empty($uTicket['count']) || $uTicket['count'] < $item['count']){
						$uTicket['count'] = 0;
						$isOutTicket = true;
					}else{
						$uTicket['count'] = $uTicket['count'] - $item['count'];
					}

					$this->user_ticket_model->updateRecord($uTicket, 'id');
				}
			}

			if ($isOutTicket) {
				$group_name = $this->getGroupName($shift_frame_id);
				$user = $this->user_model->getFromId($user_id);
				$title = "チケット不足のお客様の出席";
				$body = "グループ".$group_name."にて、".$user['user_nick']."さんのチケットが0以下で出席処理がされました。";

				$staffs = $this->staff_organ_model->getStaffsByPermission($order['organ_id'], STAFF_AUTH_BOSS);

				if (!empty($staffs)) {
					$group_name = $this->getGroupName($shift_frame_id);
					$user = $this->user_model->getFromId($user_id);

					foreach($staffs as $staff){
						if (!empty($staff['staff_mail'])) {
							$this->sendMailMessage($title, $body, $staff['staff_mail']);
						}
					}
				}
			}

			$userTickets = $this->user_ticket_model->getUserTicket(['user_id' => $user_id]);

			$order['status'] = ORDER_STATUS_TABLE_COMPLETE;
			$this->order_model->updateRecord($order, 'id');
		}

		return;		
    }

	private function getGroupName($shift_frame_id)
	{
		$shift_frame_group = $this->shift_frame_group_model->getOneByParam(['shift_frame_id' => $shift_frame_id]);
		if (empty($shift_frame_group['group_id'])) return '未知の';
		
		$group = $this->group_model->getFromId($shift_frame_group['group_id']);
		if (empty($group['group_name'])) return '未知の';

		return $group['group_name'];
	}

}
?>