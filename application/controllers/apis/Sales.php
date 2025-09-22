<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Sales extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('user_read_news_model');
    }

    public function isNewSaleNews()
    {
		$user_id = $this->input->post('user_id');

		if (empty($user_id)) {

			$results['is_load'] = true;
			$results['is_new'] = false;
			echo(json_encode($results));
			return;
		}

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://conceptbar.info/app/wp-json/api/app/load_news?app_user=".$user_id);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);

		if(curl_errno($ch)){
			$results['is_load'] = true;
			$results['is_new'] = false;
			echo(json_encode($results));
			return;
		}

		curl_close($ch);

		$result = json_decode( $response, true);

		$isNew = false;
		if (!empty($result['is_new'])) {
			$isNew = $result['is_new'];
		}

/*		$news = [];

		$reads = $this->user_read_news_model->getDataByParam(['user_id'=> $user_id]);
		if (empty($reads)) {
			$readIds = [];
		}else{
			$readIds = array_column($reads, 'news_id');
		}

		$isNew = false;
		foreach($news as $new){
			if (array_search($new, $readIds)===false) {
				$isNew = true;
				break;
			}
		}*/

		$results['is_load'] = true;
        $results['is_new'] = $isNew;

        echo(json_encode($results));
    }

}
?>
