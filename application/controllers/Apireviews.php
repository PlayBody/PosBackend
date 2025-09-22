<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/WebController.php';

/*
 *
 */

class Apireviews extends WebController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('menu_review_model');
    }

    public function loadMenuReview(){
        $user_id = $this->input->post('user_id');
        $menu_id = $this->input->post('menu_id');

        $review = $this->menu_review_model->getRecordByCond(['user_id'=>$user_id, 'menu_id'=>$menu_id]);
        if (empty($review)){
            $results['isLoad'] = false;
            echo json_encode($results);
            return;
        }

        $results['isLoad'] = true;
        $results['review'] = $review;

        echo json_encode($results);

    }

    public function saveMenuReview(){
        $user_id = $this->input->post('user_id');
        $menu_id = $this->input->post('menu_id');
        $service_mark = $this->input->post('service_mark');
        $price_mark = $this->input->post('price_mark');
        $content = $this->input->post('content');

        $review = $this->menu_review_model->getRecordByCond(['user_id'=>$user_id, 'menu_id'=>$menu_id]);

        if (empty($review)){
            $review = array(
                'user_id'=>$user_id,
                'menu_id'=>$menu_id,
                'service_review' => $service_mark,
                'price_review' => $price_mark,
                'review_content'=>$content
            );

            $this->menu_review_model->insertRecord($review);
        }else{
            $review['service_review'] = $service_mark;
            $review['price_review'] = $price_mark;
            $review['review_content'] = $content;

            $this->menu_review_model->updateRecord($review, 'menu_review_id');
        }

        $results['isSave'] = true;

        echo json_encode($results);

    }



}
?>