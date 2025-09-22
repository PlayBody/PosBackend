<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Coupon_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'coupons';
        $this->primary_key = 'coupon_id';
    }

    public function getListByCondition($cond){

        $this->db->select($this->table.".*, IF(staffs.staff_nick is NULL, 
                CONCAT(staffs.staff_first_name,' ', staffs.staff_last_name), 
                staffs.staff_nick
            ) as staff_name");
        $this->db->from($this->table);

        $this->db->join('staffs', 'staffs.staff_id=coupons.staff_id', 'left');


        if (!empty($cond['company_id'])){
            $this->db->where('coupons.company_id', $cond['company_id']);
        }

        if (!empty($cond['user_id'])){
            $this->db->where('user_id', $cond['user_id']);
        }

        if (!empty($cond['visible'])){
            $this->db->where('coupons.visible', $cond['visible']);
        }

        $this->db->order_by('create_date', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getCouponbyCondition($cond){
        $this->db->from($this->table);

        if (!empty($cond['is_use'])){
            $this->db->where('is_use', $cond['is_use']);
        }
        if (!empty($cond['company_id'])){
            $this->db->where('company_id', $cond['company_id']);
        }
        if (!empty($cond['user_id'])){
            $this->db->where('user_id', $cond['user_id']);
        }
        if (!empty($cond['coupon_code'])){
            $this->db->where('coupon_code', $cond['coupon_code']);
        }
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getUserCoupon($user_id, $coupon_code){
        $this->db->select($this->table.'.*, user_coupons.user_id');
        $this->db->from($this->table);
        $this->db->join('user_coupons', 'user_coupons.coupon_id = coupons.coupon_id', 'right');

        $this->db->where('coupon_code', $coupon_code);
        $this->db->where('user_coupons.user_id', $user_id);

        $query = $this->db->get();
        return $query->row_array();
    }

}