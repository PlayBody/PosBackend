<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apisums extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {

        parent::__construct();

        $this->load->model('history_table_model');
        $this->load->model('history_table_menu_model');
        $this->load->model('user_model');
        $this->load->model('order_model');
        $this->load->model('order_menu_model');
    }

    public function loadSumSales()
    {
        $organ_id = $this->input->post('organ_id');
        $select_year = $this->input->post('select_year');
        $select_month = $this->input->post('select_month');
        $from_day = $this->input->post('from_day');
        $to_day = $this->input->post('to_day');

        $results = [];
        if (empty($organ_id) || empty($select_year) || empty($select_month) || empty($from_day) || empty($to_day)){
            $results['isLoaded'] = false;
            echo json_encode($results);
            return;
        }

        $weekAry = array('', '月', '火', '水', '木', '金', '土', '日');
        if ($select_month<10) $select_month = "0".$select_month;
        $graphs = [];
        for ($i=$from_day; $i<=$to_day;$i++){
            $tmp = [];

            $i_day = $i;
            if ($i_day<10) $i_day = "0".$i_day;

            $sel_date = $select_year . "-" . $select_month . "-" . $i_day;
            $amount = $this->order_model->getOrderAmountByDate($organ_id, $sel_date);

            $curDateTime = new DateTime($sel_date);
            $week = $curDateTime->format("N");


            $key = $i;
            $graphs[$key]['all'] = 0;
            $graphs[$key]['cnt'] = 0;
            $graphs[$key]['yobi'] = $i."\n(".$weekAry[$week].")";
            $graphs[$key]['average'] = 0;
            if (!empty($amount['amount'])){
                 $graphs[$key]['all'] = (int)$amount['amount'];
                 $graphs[$key]['cnt'] = (int)$amount['order_count'];
                 if (!empty($amount['order_count']))
                    $graphs[$key]['average'] = (int)($amount['amount'] / $amount['order_count']);
            }

        }


        $results['isLoaded'] = true;
        $results['graphs'] = $graphs;

        echo json_encode($results);
        return;
    }

    public function loadSumSaleDetail()
    {
        $select_date = $this->input->post('select_date');
        $organ_id = $this->input->post('organ_id');

        $results = [];
        if (empty($organ_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $sales = $this->order_model->getSaleDetail($organ_id, $select_date);

        $sum_amount = $this->order_model->getTodayHistoryAmount($organ_id, $select_date);

        $results['isLoad'] = true;
        $results['sales'] = $sales;
        $results['sum_amount'] = $sum_amount;

        echo json_encode($results);
    }

    public function deleteSale(){
        $order_id = $this->input->post('order_id');
        if (empty($order_id)){
            $results['isDelete'] = false;

            echo json_encode($results);
            return;
        }
        $this->order_menu_model->delete_force($order_id, 'order_id');
        $this->order_model->delete_force($order_id, 'id');
        $results['isDelete'] = true;
        echo json_encode($results);
    }

    public function loadSumSaleItem()
    {
        $order_id = $this->input->post('order_id');

        $results = [];
        if (empty($order_id)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $order = $this->order_model->getFromId($order_id);

        $results['isLoad'] = true;

        if (!empty($order['user_id'])){
            $user = $this->user_model->getFromId($order['user_id']);
            $results['user'] = $user;
        }

        $menus = $this->order_menu_model->getDataByParam(['order_id'=>$order_id]);

        $results['order'] = $order;
        $results['menus'] = empty($menus) ? [] : $menus;

        echo json_encode($results);
    }
}
?>
