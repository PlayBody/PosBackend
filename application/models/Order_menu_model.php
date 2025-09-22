<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Order_menu_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'order_menus';
        $this->primary_key = 'id';
    }

    function getWhere($cond){
        if (!empty($cond['order_id'])){
            $this->db->where('order_menus.order_id', $cond['order_id']);
        }
        if (!empty($cond['is_goods'])){
            $this->db->where('menus.is_goods', '1');
        }
        if (!empty($cond['is_service'])){
            $this->db->where('menus.is_goods', '0');
        }
        if(!empty($cond['select_date'])){
            $this->db->where("orders.from_time like '" . $cond['select_date']." %'");
        }
        if(!empty($cond['status_array'])){
            $this->db->where('status in ('. join(',', $cond['status_array']).')');
        }
    }

    function getListByCond($cond)
    {
        $this->db->select($this->table.'.*, menus.menu_title, categories.*')->from($this->table);
        $this->db->join('menus', 'menus.menu_id=order_menus.menu_id', 'left');
        $this->db->join('categories', 'menus.category_id=categories.id', 'left');
        $this->getWhere($cond);

        $query = $this->db->get();

        return $query->result_array();
    }
    function getMenuAmount($cond){
        $this->db->select('sum(order_menus.menu_price*order_menus.quantity) as amount');
        $this->db->from($this->table);
        $this->db->join('orders', 'orders.id=order_menus.order_id', 'left');
        $this->db->join('menus', 'menus.menu_id=order_menus.menu_id', 'left');

        $this->getWhere($cond);

        $query = $this->db->get();
        $record = $query->row_array();

        return empty($record['amount']) ? 0 : $record['amount'];

    }

    function getFirstMenu($order_id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->join('menus', 'order_menus.menu_id = menus.menu_id', 'left');
        $this->db->join('categories', 'categories.id = menus.category_id', 'left');

        $this->db->where('order_menus.order_id', $order_id);
        
        $query = $this->db->get();

        return $query->row_array();
    }
	
}
