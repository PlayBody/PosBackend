<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apisquare extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
		
		$this->load->model('user_model');
		$this->load->model('card_model');
    }

    public function addPayment(){
        $amount = $this->input->post('amount');
        $currency = $this->input->post('currency');
        $idempotency_key = $this->input->post('idempotency_key');
        $source_id = $this->input->post('source_id');
        $customer_id = $this->input->post('customer_id');
		$is_card_save = $this->input->post('is_save_card');
		$user_id = $this->input->post('user_id');
		$auth_token = $this->input->post('auth_token');
		$postal_code = $this->input->post('postal_code');
		

        $api_url = SQUARE_ENDPOINT_HOST . 'v2/payments';
        $headers = array(
            'Content-Type:application/json',
            'Authorization: Bearer ' . $auth_token
        );

        $data = array(
            'amount_money'=>array(
                'amount' => intval($amount),
                'currency' => $currency
            ),
            'idempotency_key' => $idempotency_key,
            'source_id' => $source_id,

        );
		if(!empty($customer_id)) $data['customer_id'] = $customer_id;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);

		$is_pay = false;
        if (curl_errno($ch)) {
            $is_pay = false;
        } else {
			$result = json_decode($result, true);
			if(empty($result['payment']) || empty($result['payment']['id'])){
			}else{
				$is_pay = true;
				if($is_card_save){
					$payment_id = $result['payment']['id'];

					$customer_id = $this->createCustomer($auth_token, $user_id, $postal_code);
					if(!empty($customer_id)){
						$card_id = $this->createCard($auth_token, $idempotency_key, $payment_id, $customer_id, $postal_code);
						if (!empty($card_id)){
							$card = array(
									'user_id' => $user_id,
									'brand' => $result['payment']['card_details']['card']['card_brand'],
									'last_num' => $result['payment']['card_details']['card']['last_4'],
									'exp_month' => $result['payment']['card_details']['card']['exp_month'],
									'exp_year' => $result['payment']['card_details']['card']['exp_year'],
									'type' => $result['payment']['card_details']['card']['card_type'],
									'postal_code' => $postal_code,
									'customer_id' => $customer_id,
									'source_id' => $card_id,
									
								);
							$this->card_model->insertRecord($card);
						}
					}
				}
			}
        }
		
		$results['isPay'] = $is_pay;
		$results['paymentData'] = $result;
        echo json_encode($results);
    }

	private function createCustomer($auth_token, $user_id, $postal_code){
		
		$user = $this->user_model->getFromId($user_id);

		$customer_id = $this->searchCustomer($auth_token, $user['user_email']);
		if(!empty($customer_id)) return $customer_id;

        $api_url = SQUARE_ENDPOINT_HOST . 'v2/customers';
        $headers = array(
            'Content-Type:application/json',
            'Authorization: Bearer ' . $auth_token
        );

		$data = array(
			'given_name' => empty($user['user_first_name']) ? 'unkown' : $user['user_first_name'],
			'family_name' => empty($user['user_last_name']) ? 'unkown' : $user['user_last_name'],
			'email_address' => empty($user['user_email']) ? 'unkown' : $user['user_email'],
            'address'=>array(
                'postal_code' => $postal_code,
            ),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);

		$customer_id = null;
        if (curl_errno($ch)) {
        } else {
			$result = json_decode($result, true);
			$customer_id = empty($result['customer']['id']) ? '' : $result['customer']['id'];
        }
        return $customer_id;
	}

	public function searchCustomer($auth_token, $mail){

        $api_url = SQUARE_ENDPOINT_HOST . 'v2/customers/search';
        $headers = array(
            'Content-Type:application/json',
            'Authorization: Bearer ' . $auth_token
        );

		$data = array(
            'query'=>array(
                'filter' => array(
					'email_address' => array(
						'fuzzy' => $mail
					)
				),
            ),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);
		$customer_id = null;
        if (curl_errno($ch)) {
        } else {
			$result = json_decode($result, true);
			if(!empty($result['customers'])){
				$customer_id = $result['customers'][0]['id'];
			}
        }
		
        return $customer_id;
	}


	private function createCard($auth_token, $idempotency_key, $source_id, $customer_id, $postal_code){

        $api_url = SQUARE_ENDPOINT_HOST . 'v2/cards';
        $headers = array(
            'Content-Type:application/json',
            'Authorization: Bearer ' . $auth_token
        );

		$data = array(
			'idempotency_key' => $idempotency_key,
			'source_id' => $source_id,
            'card'=>array(
                'billing_address' => array(
						'postal_code' => $postal_code,
					),
				'customer_id' => $customer_id
            ),
            //'phone_number' => empty($user['user_tel']) ? 'unkown' : $user['user_tel'],
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);
		
		$card_id = null;
        if (curl_errno($ch)) {
        } else {
			$result = json_decode($result, true);
			$card_id = empty($result['card']['id']) ? '' : $result['card']['id'];
        }
        return $card_id;
	}

	public function loadCardList(){
		$user_id = $this->input->post('user_id');
		$cards = $this->card_model->getDataByParam(['user_id' => $user_id]);

		$results['cards'] = $cards;
		echo json_encode($results);
	}
}
?>