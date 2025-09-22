<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Order_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'orders';
        $this->primary_key = 'id';
    }

    public function getWhere($cond){
        if (!empty($cond['id'])){
            $this->db->where('orders.id', $cond['id']);
        }
        if (!empty($cond['organ_id'])){
            $this->db->where('orders.organ_id', $cond['organ_id']);
        }
        if (!empty($cond['from_time'])){
            $this->db->where('orders.from_time>=', $cond['from_time']);
        }
        if (!empty($cond['to_time'])){
            $this->db->where('orders.to_time<=', $cond['to_time']);
        }
        if (!empty($cond['equal_from_time'])){
            $this->db->where('orders.from_time', $cond['equal_from_time']);
        }
        if (!empty($cond['equal_to_time'])){
            $this->db->where('orders.to_time', $cond['equal_to_time']);
        }
        if(!empty($cond['status_array'])){
            $this->db->where('status in ('. join(',', $cond['status_array']).')');
        }
        if(!empty($cond['pay_method'])){
            $this->db->where('pay_method', $cond['pay_method']);
        }
        if(!empty($cond['select_date'])){
            $this->db->where("from_time like '" . $cond['select_date']." %'");
        }
        if(!empty($cond['is_shift_frame_mode'])){
            $this->db->where("shift_frame_id is not NULL and shift_frame_id > 0");
        }
		if(!empty($cond['is_not_users'])){
            $this->db->where("user_id not in (" . $cond['is_not_users'] . ")");
        }
        if(!empty($cond['is_group_start'])){
            $this->db->where("is_group_start", 1);
        }
        if(!empty($cond['status'])){
            $this->db->where("status", $cond['status']);
        }
    }

    public function getListByCond($cond){
        $this->db->select($this->table.".*, organs.organ_name, IF(CONCAT(users.user_first_name,' ', users.user_last_name) is NULL, 
                users.user_nick, 
                CONCAT(users.user_first_name,' ', users.user_last_name)
            ) as user_name, 
            IF(staffs.staff_nick is NULL, 
                CONCAT(staffs.staff_first_name,' ', staffs.staff_last_name), 
                staffs.staff_nick
            ) as staff_name, staffs.staff_id, 
            users.*,
            staffs.staff_sex,
            if(table_names.table_name is NULL, CONCAT('seat', orders.table_position), table_names.table_name) as table_name
            ");
        $this->db->from($this->table);
        $this->db->join('organs', 'organs.organ_id = orders.organ_id', 'left');
        $this->db->join('staffs', 'staffs.staff_id = orders.select_staff_id', 'left');
        $this->db->join('users', 'users.user_id = orders.user_id', 'left');
        $this->db->join('table_names', 'table_names.organ_id = orders.organ_id and table_names.table_position=orders.table_position', 'left');


        if (!empty($cond['company_id'])){
            $this->db->where("organs.company_id",  $cond['company_id']);
        }
        if (!empty($cond['order_id'])){
            $this->db->where("orders.id",  $cond['order_id']);
        }
        if (!empty($cond['organ_ids'])){
            $this->db->where("orders.organ_id in (". $cond['organ_ids'] .")");
        }
        if (!empty($cond['organ_id'])){
            $this->db->where("orders.organ_id", $cond['organ_id']);
        }
        if (!empty($cond['user_id'])){
            $this->db->where("orders.user_id", $cond['user_id']);
        }
        if (!empty($cond['staff_id'])){
            $this->db->where("orders.select_staff_id", $cond['staff_id']);
        }else{
            if (key_exists('staff_id', $cond)){
                $this->db->where("orders.select_staff_id is null");
            }
        }

        if (!empty($cond['is_select_staff'])){
            $this->db->where("orders.select_staff_id is not null");
        }

        if (!empty($cond['from_time'])){
            $this->db->where("orders.from_time >= '". $cond['from_time'] . "'");
        }
        if (!empty($cond['is_waiting'])){
            $this->db->where("orders.is_waiting", $cond['is_waiting']);
        }
        if (!empty($cond['to_time'])){
            if (empty($cond['is_with_interval'])){
                $this->db->where("orders.to_time < '". $cond['to_time'] . "'");
            }else{
                $this->db->where("date_add(orders.to_time, interval orders.`interval` minute)  < '". $cond['to_time'] . "'");
            }
        }

        if (!empty($cond['in_from_time']) && !empty($cond['in_to_time'])){
            if (empty($cond['is_with_interval'])){
                $this->db->where("to_time >'". $cond['in_from_time'] ."' and from_time <'". $cond['in_to_time'] ."'" );
            }else{
                $this->db->where("date_add(orders.to_time, interval orders.`interval` minute) >'". $cond['in_from_time'] ."' and from_time <'". $cond['in_to_time'] ."'" );
            }
        }

        if (!empty($cond['select_time'])){
            $this->db->where("orders.from_time<= '". $cond['select_time'] . "'");
            $this->db->where("orders.to_time>'". $cond['select_time'] . "'");
        }

        if(!empty($cond['status_array'])){
            $this->db->where('orders.status in ('. join(',', $cond['status_array']).')');
        }
        if(!empty($cond['self_order_id'])){
            $this->db->where('orders.id <> '. $cond['self_order_id']);
        }
        if (!empty($cond['shift_frame_id'])){
            $this->db->where("orders.shift_frame_id", $cond['shift_frame_id']);
        }
        //$this->db->where('orders.from_time <> orders.to_time');

        $this->db->order_by($this->table.'.from_time', 'asc');
        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }
//
//    public function getRecordByCond($cond){
//        $this->db->select($this->table.".*, IF(staffs.staff_nick is NULL,
//                CONCAT(staffs.staff_first_name,' ', staffs.staff_last_name),
//                staffs.staff_nick
//            ) as staff_name,
//            IF(CONCAT(users.user_first_name,' ', users.user_last_name) is NULL,
//                users.user_nick,
//                CONCAT(users.user_first_name,' ', users.user_last_name)
//            ) as user_name");
//        $this->db->from($this->table);
//        $this->db->join('staffs', 'staffs.staff_id = orders.select_staff_id', 'left');
//        $this->db->join('users', 'users.user_id = orders.user_id', 'left');
//        if (!empty($cond['order_id'])){
//            $this->db->where('id', $cond['order_id']);
//        }
////        if (!empty($cond['organ_id'])){
////            $this->db->where('organ_id', $cond['organ_id']);
////        }
////        if (!empty($cond['table_position'])){
////            $this->db->where('orders.table_position', $cond['table_position']);
////        }
////        if (!empty($cond['select_time'])){
////            $this->db->where("from_time <='". $cond['select_time'] ."'");
////            $this->db->where("to_time >'". $cond['select_time'] ."'");
////        }
////        if(!empty($cond['status_array'])){
////            $this->db->where('status in ('. join(',', $cond['status_array']).')');
////        }
//
//        $query = $this->db->get();
//        return $query->row_array();
//    }

    public function getOrderRecord($cond){
        $this->db->select($this->table.".*, IF(staffs.staff_nick is NULL, 
                CONCAT(staffs.staff_first_name,' ', staffs.staff_last_name), 
                staffs.staff_nick
            ) as staff_name, 
             IF(CONCAT(users.user_first_name,' ', users.user_last_name) is NULL, 
                users.user_nick, 
                CONCAT(users.user_first_name,' ', users.user_last_name)
            ) as user_name,
            users.*,
            staffs.staff_first_name, staffs.staff_last_name, 
            orders.create_date as reg_date
            ");
        $this->db->from($this->table);
        $this->db->join('staffs', 'staffs.staff_id = orders.select_staff_id', 'left');
        $this->db->join('users', 'users.user_id = orders.user_id', 'left');

        if (!empty($cond['order_id'])){
            $this->db->where('id', $cond['order_id']);
        }
        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }
        if (!empty($cond['table_position'])){
            $this->db->where('orders.table_position', $cond['table_position']);
        }
        if (!empty($cond['select_time'])){
            $this->db->where("from_time <='". $cond['select_time'] ."'");
            $this->db->where("to_time >'". $cond['select_time'] ."'");
        }
        if(!empty($cond['status_array'])){
            $this->db->where('status in ('. join(',', $cond['status_array']).')');
        }

        if (!empty($cond['shift_frame_id'])){
            $this->db->where("orders.shift_frame_id", $cond['shift_frame_id']);
        }
        if (!empty($cond['user_id'])){
            $this->db->where("orders.user_id", $cond['user_id']);
        }
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getOrderAmountByDate($organ_id, $date){
        $this->db->select('sum(amount) as amount, count(id) as order_count');
        $this->db->from($this->table);

        $this->db->where('organ_id', $organ_id);
        $this->db->where("from_time like '" . $date . " %'");
        $this->db->where('status', ORDER_STATUS_TABLE_COMPLETE);

        $query = $this->db->get();
        return $query->row_array();
    }

    public function getSaleDetail($organ_id, $date){
        $this->db->select('orders.id, amount, from_time, table_position, count(order_menus.id) as menu_count, user_count');
        $this->db->from($this->table);
        $this->db->join('order_menus', 'orders.id=order_menus.order_id', 'left');
        $this->db->where('organ_id', $organ_id);
        $this->db->where("from_time like '" . $date . " %'");
        $this->db->where('status', ORDER_STATUS_TABLE_COMPLETE);
        $this->db->group_by('orders.id');
        $this->db->order_by('orders.from_time');

        $query = $this->db->get();

        return $query->result_array();

    }

    public function getTodayHistoryAmount($organ_id, $date){
        $this->db->select('sum(amount) as amount');
        $this->db->from($this->table);
        $this->db->where('organ_id', $organ_id);

        $this->db->where('pay_method', 1);
        $this->db->where("from_time like '" . $date . " %'");
        $this->db->where('status', ORDER_STATUS_TABLE_COMPLETE);

        $query = $this->db->get();
        $result = $query->row_array();

        if (empty($result['amount'])) return 0;

        return $result['amount'];

    }

    public function emptyMaxPosition($cond){
        $this->db->select('max(table_position) as max_pos');
        $this->db->from($this->table);

        if (!empty($cond['order_id'])){
            $this->db->where("orders.id <> ". $cond['order_id']);
        }

        if (!empty($cond['organ_id'])){
            $this->db->where("orders.organ_id", $cond['organ_id']);
        }

        if (!empty($cond['from_time'])){
            $this->db->where("((to_time >'". $cond['from_time'] ."' and from_time <'". $cond['to_time'] ."') || (from_time ='". $cond['from_time'] ."' and to_time ='". $cond['to_time'] ."'))" );
        }

        if(!empty($cond['status_array'])){
            $this->db->where('orders.status in ('. join(',', $cond['status_array']).')');
        }

        $query = $this->db->get();
        $result = $query->row_array();

        return empty($result['max_pos']) ? 1 : ($result['max_pos']+1);

    }

    public function getAmount($cond){
        $this->db->select('sum(amount) as amount');
        $this->db->from($this->table);

        $this->getWhere($cond);

        $query = $this->db->get();
        $record = $query->row_array();

        return empty($record['amount']) ? 0 : $record['amount'];
    }

    public function getCompleteOrdersByStaff($staff_id, $organ_id, $from_time, $to_time){
        $this->db->from($this->table);
        $this->db->where('select_staff_id', $staff_id);
        $this->db->where('organ_id', $organ_id);
        $this->db->where('status', ORDER_STATUS_TABLE_COMPLETE);

        $this->db->where("from_time < '$to_time'");
        $this->db->where("to_time > '$from_time'");

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getPositionCountByPeriod($organ_id, $from_time, $to_time){
        $this->db->select('count(table_position) as position_count');
        $this->db->from($this->table);
        $this->db->where('organ_id', $organ_id);
        $this->db->where("from_time < '$to_time'");
        $this->db->where("to_time > '$from_time'");
        $this->db->where("table_position is not null");

        $this->db->group_by('table_position');

        $query = $this->db->get();
        $result = $query->row_array();
        if (empty($result)) return 0;
        return $result['position_count'];
    }

    public function isStaffInReserve($staff_id, $from_time, $to_time){
        $this->db->from($this->table);
        $this->db->where('select_staff_id', $staff_id);
        $this->db->where("from_time < '$to_time'");
        //$this->db->where("to_time > '$from_time'");

        $this->db->where("date_add(orders.to_time, interval orders.`interval` minute)  > '". $from_time . "'");
        $this->db->where('status in ('. ORDER_STATUS_RESERVE_REQUEST .','. ORDER_STATUS_RESERVE_APPLY.')');

        $query = $this->db->get();
        return !empty($query->row_array());
    }

    public function getOrderTables($organ_id, $from_time, $to_time){
        $this->db->select("table_position");
        $this->db->from($this->table);
        $this->db->where('organ_id', $organ_id);
        $this->db->where("from_time <= '$to_time' and to_time >'".$from_time."'");
        $this->db->where('status in ('. ORDER_STATUS_RESERVE_REQUEST .','. ORDER_STATUS_RESERVE_APPLY.')');
        $this->db->group_by('table_position');

        $query = $this->db->get();
        $results = $query->result_array();

        $tables = [];
        foreach ($results as $item){
            $tables[] = $item['table_position'];
        }

        return $tables;
    }

    public function getOrderRow($cond){
        $this->db->select('orders.*, organs.*, staffs.*, users.*, table_names.table_name');
        $this->db->from($this->table);
        $this->db->join('organs', 'organs.organ_id = orders.organ_id', 'left');
        $this->db->join('staffs', 'staffs.staff_id = orders.select_staff_id', 'left');
        $this->db->join('users', 'users.user_id = orders.user_id', 'left');
        $this->db->join('table_names', 'table_names.organ_id = orders.organ_id and table_names.table_position=orders.table_position', 'left');

        $this->getWhere($cond);

        $query = $this->db->get();
        return $query->row_array();
    }

    public function getListData($cond){
        $this->db->from($this->table);
        $this->getWhere($cond);
       $this->db->order_by('from_time');

        $query = $this->db->get();
        return $query->result_array();
    }

	public function getOrdersByPositionNoComplete($organ_id,$position, $order_id)
	{
        $this->db->from($this->table);
        $this->db->where('organ_id', $organ_id);
        $this->db->where('table_position', $position);
		$this->db->where('id <> '. $order_id);
		$this->db->where('status <> '. ORDER_STATUS_TABLE_COMPLETE);
        $query = $this->db->get();
        return $query->result_array();
	}

	public function isExistOrderByOrganAndPosition($organ_id,$position)
	{
        $this->db->from($this->table);
        $this->db->where('organ_id', $organ_id);
        $this->db->where('table_position', $position);
		$this->db->where('status <> '. ORDER_STATUS_TABLE_COMPLETE);
        $query = $this->db->get();
        $result = $query->result_array();
		return !empty($result);
	}

	public function deleteOrder($cond)
	{
        if (!empty($cond['shift_frame_ids'])){
            $this->db->where("shift_frame_id In (". implode(',' , $cond['shift_frame_ids']) .")");
        }

        if (!empty($cond['shift_frame_id'])){
            $this->db->where("shift_frame_id", $cond['shift_frame_id']);
        }

		if (!empty($cond['user_id'])){
            $this->db->where("user_id", $cond['user_id']);
        }

		if (!empty($cond['organ_id'])){
            $this->db->where("organ_id", $cond['organ_id']);
        }


        $this->db->delete($this->table);
	}
}
