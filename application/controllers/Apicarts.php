<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apicarts extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('cart_model');
        $this->load->model('cart_detail_model');
    }

    public function addCart(){
        $user_id = $this->input->post('user_id');
        $company_id = $this->input->post('company_id');

        $ticket_id = $this->input->post('ticket_id');
        $count = $this->input->post('count');

        $cart = $this->cart_model->getRecordByCond(['user_id'=>$user_id, 'visible'=>'1', 'cart_type'=>'1']);

        if (empty($cart)){
            $cart = array(
                'user_id'=>$user_id,
                'company_id'=>$company_id,
                'amount' => '0',
                'cart_type' => '1',
                'visible' => '1',
            );
            $cart_id = $this->cart_model->insertRecord($cart);
        }else{
            $cart_id = $cart['cart_id'];
//            $cart['amount'] = $cart['amount'] + $amount;
//            $this->cart_model->updateRecord($cart, 'cart_id');
        }

        $cart_detail = $this->cart_detail_model->getRecordByCond(['cart_id'=>$cart_id, 'ticket_id'=>$ticket_id]);

        if (empty($cart_detail)){
            $cart_detail = array(
                'cart_id'=>$cart_id,
                'ticket_id'=>$ticket_id,
                'count' => $count,
            );

            $this->cart_detail_model->insertRecord($cart_detail);
        }else{
            $cart_detail['count'] = $cart_detail['count'] + $count;
            $this->cart_detail_model->updateRecord($cart_detail, 'cart_detail_id');
        }

        $all_amount = $this->cart_detail_model->getAllCartSum(['cart_id'=>$cart_id]);
        $cart = $this->cart_model->getFromId($cart_id);
        $cart['amount'] = $all_amount;
        $this->cart_model->updateRecord($cart, 'cart_id');

        $results['isSave'] = true;
        echo json_encode($results);
    }


    public function updateCart(){
        $user_id = $this->input->post('user_id');

        $cart = $this->cart_model->getRecordByCond(['user_id'=>$user_id, 'visible'=>'1', 'cart_type'=>'1']);
        if (empty($cart)){
            $results['isUpdate'] = false;
        }else{
            $cart['cart_type'] = 2;
            $this->cart_model->updateRecord($cart, 'cart_id');

            $details = $cart_detail = $this->cart_detail_model->getListByCond(['cart_id'=>$cart['cart_id']]);
            $this->load->model('user_ticket_model');

            foreach ($details as $detail) {
                $user_ticket = $this->user_ticket_model->getUserTicket(['user_id'=>$user_id, 'ticket_id'=>$detail['ticket_id']]);
                if (empty($user_ticket)){
                    $user_ticket = array(
                        'user_id' => $user_id,
                        'ticket_id' => $detail['ticket_id'],
                        'count' => $detail['count']*$detail['ticket_count'],
                        'is_reset' => 0,
                        'reset_time_type' => 0,
                        'reset_time_value' => 0,
                        'reset_count' => 0
                    );
                    $this->user_ticket_model->insertRecord($user_ticket);
                }else{
                    $user_ticket['count'] += $detail['ticket_count'];

                    $this->user_ticket_model->updateRecord($user_ticket, 'id');
                }
            }
            $results['isUpdate'] = true;
        }

        echo json_encode($results);
    }

    public function getSumCart(){
        $user_id = $this->input->post('user_id');

        $cart = $this->cart_model->getRecordByCond(['user_id'=>$user_id, 'visible'=>'1', 'cart_type'=>'1']);
        if (empty($cart)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $details = $this->cart_detail_model->getListByCond(['cart_id'=>$cart['cart_id']]);

        $results['isLoad'] = true;

        $results['count'] = count($details);
        $results['all_amount'] = $cart['amount'];

        echo json_encode($results);
    }
    public function getCarts(){
        $user_id = $this->input->post('user_id');

        $cart = $this->cart_model->getRecordByCond(['user_id'=>$user_id, 'visible'=>'1', 'cart_type'=>'1']);
        if (empty($cart)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $details = $this->cart_detail_model->getListByCond(['cart_id'=>$cart['cart_id']]);

        $results['isLoad'] = true;

        $results['details'] = $details;

        echo json_encode($results);
    }

    public function updateCartDetail(){
        $cart_detail_id = $this->input->post('id');
        $count = $this->input->post('count');

        if (empty($cart_detail_id)){
            echo json_encode(['isSave'=>false]);
            return;
        }

        $detail = $this->cart_detail_model->getFromId($cart_detail_id);

        $detail['count'] = empty($count) ? '1' : $count;

        $this->cart_detail_model->updateRecord($detail, 'cart_detail_id');
        $all_amount = $this->cart_detail_model->getAllCartSum(['cart_id'=>$detail['cart_id']]);
        $cart = $this->cart_model->getFromId($detail['cart_id']);
        $cart['amount'] = $all_amount;
        $this->cart_model->updateRecord($cart, 'cart_id');

        echo json_encode(['isSave'=>true]);
    }

    public function deleteCartDetail(){
        $cart_detail_id = $this->input->post('id');

        if (empty($cart_detail_id)){
            echo json_encode(['isDelete'=>false]);
            return;
        }

        $detail = $this->cart_detail_model->delete_force($cart_detail_id, 'cart_detail_id');

        echo json_encode(['isDelete'=>true]);
    }
}
?>