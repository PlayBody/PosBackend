<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';
class User_coupon_model extends Base_model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'user_coupons';
        $this->primary_key = 'user_coupon_id';
    }

    public function getUserCoupons($cond){
        $this->db->select('coupons.*, user_coupons.user_coupon_id, user_coupons.use_flag');
        $this->db->from($this->table);
        $this->db->join('coupons', 'coupons.coupon_id=user_coupons.coupon_id','inner');

        if (!empty($cond['user_id'])){
            $this->db->where('user_coupons.user_id', $cond['user_id']);
        }
        if (!empty($cond['use_flag'])){
            $this->db->where('user_coupons.use_flag', $cond['use_flag']);
        }

        if (!empty($cond['use_date'])){
            $this->db->where("coupons.use_date >=", $cond['use_date']);
        }

        if (!empty($cond['use_organ'])){
            $this->db->where("(coupons.use_organ_id =". $cond['use_organ'] ." OR coupons.use_organ_id=0)");
        }

        if (!empty($cond['upper_amount'])){
            $this->db->where("coupons.upper_amount <=" . $cond['use_organ'] );
        }
        if (!empty($cond['is_user'])){
            $this->db->where("coupons.visible = 1");
        }


        $this->db->order_by('coupons.use_date', 'desc');

        $query = $this->db->get();

        return $query->result_array();

    }
    public function getStaffListByCoupon($cond){

        $this->db->select("staffs.*");
        $this->db->from($this->table);

        $this->db->join('staffs', 'staffs.staff_id=user_coupons.staff_id', 'inner');

        if (!empty($cond['coupon_id'])){
            $this->db->where('user_coupons.coupon_id', $cond['coupon_id']);
        }

        $this->db->group_by('user_coupons.staff_id');
        $query = $this->db->get();

        return $query->result_array();

    }
}

  