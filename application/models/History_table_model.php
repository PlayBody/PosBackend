<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class History_table_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'history_tables';
        $this->primary_key = 'order_table_history_id';
    }

    public function getOrderAmount($organ_id, $from_date, $to_date){

        $sql = "SELECT count(history_tables.order_table_history_id) as customer_count, sum(amount) as amount
            FROM history_tables 
            WHERE history_tables.organ_id = '" . $organ_id . "'
            and history_tables.start_time>='" . $from_date . " 00:00:00' and history_tables.start_time<='" . $to_date . " 23:59:59'
            and history_tables.end_time>='" . $from_date . " 00:00:00' and history_tables.end_time<='" . $to_date . " 23:59:59'
            and is_reject is Null
        ";

        $query = $this->db->query($sql);

        return $query->row_array();

    }

    public function getSaleDetail($select_date, $organ_id){
        $sql = "select order_table_history_id, amount, start_time, table_position, count(history_table_menu_id) as menu_count, person_count
                    from history_tables
                    LEFT JOIN history_table_menus on history_tables.order_table_history_id = history_table_menus.history_table_id
                    where start_time like '".$select_date."%'
                    and organ_id = ".$organ_id."
                    
                    GROUP BY order_table_history_id
                    ORDER BY history_tables.start_time";

        $query = $this->db->query($sql);

        return $query->result_array();

    }

    public function getTodayHistoryAmount($select_date, $organ_id){
        $this->db->select('sum(amount) as amount');
        $this->db->from($this->table);
        $this->db->where('organ_id', $organ_id);

        $this->db->where('pay_method', 1);

        $this->db->where('date(end_time)', $select_date);

        $query = $this->db->get();
        $result = $query->row_array();

        if (empty($result['amount'])) return 0;

        return $result['amount'];

    }

    public function getBackAmount($staff_id, $first_day, $last_day){

        $sql = "select sum(menu_variations.variation_back_amount) as back_amount from 
((history_tables LEFT JOIN history_table_menus on history_tables.order_table_history_id = history_table_menus.history_table_id )
left join menu_variations on history_table_menus.variation_id = menu_variations.variation_id)
left join menu_variation_backs on menu_variations.variation_id = menu_variation_backs.variation_id

where history_tables.start_time >= '".$first_day."' and history_tables.end_time < '".$last_day."'
and menu_variation_backs.staff_id=$staff_id";


        $results = $this->db->query($sql)->row_array();


        return empty($results['back_amount']) ? 0 : $results['back_amount'];
    }

    public function getSumAmountOneDay($date, $pay_method=''){
        $this->db->select("sum(amount) as sum_amount");
        $this->db->from($this->table);
        $this->db->where("end_time like '".$date."%'");
        if (!empty($pay_method)){
            $this->db->where("pay_method", $pay_method);
        }

        $query = $this->db->get();
        $row = $query->row_array();
        return empty($row['sum_amount']) ? 0 : $row['sum_amount'];
    }

    public function getRejectCount($date, $is_reject=''){
        $this->db->select("count(order_table_history_id) as reject_count");
        $this->db->from($this->table);
        $this->db->where("end_time like '".$date."%'");

        if ($is_reject=='1')
            $this->db->where("is_reject", $is_reject);

        if ($is_reject=='0')
            $this->db->where("is_reject is Null");

        $query = $this->db->get();
        $row = $query->row_array();
        return empty($row['reject_count']) ? 0 : $row['reject_count'];
    }

    public function getOrderMinusAmount($date){
        $this->db->select("sum(history_table_menus.menu_price*history_table_menus.quantity) as sum_amount");
        $this->db->from($this->table);
        $this->db->join('history_table_menus', 'history_tables.order_table_history_id=history_table_menus.history_table_id', 'left');

        $this->db->where($this->table.".end_time like '".$date."%'");
        $this->db->where("history_table_menus.menu_price < 0");

        $query = $this->db->get();
        $row = $query->row_array();
        return empty($row['sum_amount']) ? 0 : $row['sum_amount']*-1;
    }

	public function getMaxEnteringDate($user_id){
		$this->db->select("DATE_FORMAT(max(start_time), '%Y-%m-%d') as last_visit_date");
		$this->db->from($this->table);
		$this->db->where('user_id', $user_id);
		$query = $this->db->get();
		return $query->row_array();
	}

}