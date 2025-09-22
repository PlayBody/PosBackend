<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Menu_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'menus';
        $this->primary_key = 'menu_id';
    }

    public function getMenuList($cond, $count=''){
        $this->db->select($this->table.'.*, categories.*');
        $this->db->from($this->table);
        $this->db->join('organ_menus', 'organ_menus.menu_id = menus.menu_id', 'left');
        $this->db->join('categories', 'categories.id = menus.category_id', 'left');
        
        $this->db->where('visible', '1');
        if (!empty($cond['organ_id'])){
            $this->db->where('organ_menus.organ_id', $cond['organ_id']);
        }
        if (!empty($cond['company_id'])){
            $this->db->where('menus.company_id', $cond['company_id']);
        }
        if (!empty($cond['category_id'])){
            $this->db->where("menus.category_id",  $cond['category_id']);
        }
        if (!empty($cond['is_user_menu'])){
            $this->db->where('is_user_menu', $cond['is_user_menu']);
        }
        if (!empty($cond['word'])){
            $this->db->where("menu_title like '%" . $cond['word']."%'");
        }
        if (!empty($cond['menu_ids'])){
            $this->db->where("menus.menu_id in (" . $cond['menu_ids'].")");
        }

        $this->db->group_by('menus.menu_id');
        $this->db->order_by('sort_no', 'asc');

        if (!empty($count)){
            $this->db->limit($count, 0);
        }
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getAdminMenuList($cond){
        $this->db->select($this->table.'.*');
        $this->db->from($this->table);
        $this->db->join('organs', 'organs.organ_id = menus.organ_id');

       // $this->db->where('oragn_id in (1,3)');
        $this->db->where($this->table.'.visible', '1');

        if (!empty($cond['organ_ids'])){
            $this->db->where("organs.organ_id in (". $cond['organ_ids'] .")");
        }

        if (!empty($cond['company_id'])){
            $this->db->where("organs.company_id",  $cond['company_id']);
        }

        $this->db->order_by('sort_no', 'desc');

        $query = $this->db->get();

        return $query->result_array();
    }


    public function getListByCond($cond){
        $this->db->from($this->table);
        $this->db->where('visible', '1');
        if (!empty($cond['organ_id'])){
            $this->db->where('organ_id', $cond['organ_id']);
        }
        if (!empty($cond['is_user_menu'])){
            $this->db->where('is_user_menu', $cond['is_user_menu']);
        }

        $this->db->order_by('sort_no', 'asc');

        $query = $this->db->get();

        return $query->result_array();
    }


    function getMaxOrder($company_id){
        $this->db->select('sort_no')->from($this->table);

        $this->db->where('visible','1');
        $this->db->where('company_id', $company_id);

        $this->db->order_by('sort_no', 'desc');
        $query = $this->db->get();
        $result = $query->row_array();

        if (empty($result)){
            return 1;
        }

        return $result['sort_no']+1;

    }

    public function getCompanyUserMenuList($company_id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->join('organs', 'organs.organ_id=menus.organ_id', 'left');
        //$this->db->where('visible', '1');
        $this->db->where('menus.company_id', $company_id);
        $this->db->where('is_user_menu', 1);

        $this->db->order_by('sort_no', 'asc');

        $query = $this->db->get();

        return $query->result_array();
    }

    public function getMenuInfo($menu_id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->join('organs', 'organs.organ_id=menus.organ_id', 'left');
        $this->db->join('categories', 'categories.id = menus.category_id', 'left');
        //$this->db->where('visible', '1');
        $this->db->where('menus.menu_id', $menu_id);

        $query = $this->db->get();

        return $query->row_array();
    }
}
