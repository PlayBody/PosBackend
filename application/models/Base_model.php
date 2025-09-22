<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Base_model extends CI_Model
{
    protected  $table= 'tbl_company';
    protected  $primary_key= 'id';

    public function __construct()
    {
        parent::__construct();
    }

    function debug(){
        $sql =  $this->db->last_query();
        var_dump($sql);
        die;
    }
    function getListCount($where=array()){

        $this->db->from($this->table);
        if(!empty($where)) $this->db->where($where);
        return $this->db->count_all_results();
    }

    function getList($select,$where_data, $count_flag=false,$page=10, $offset=0,$order_by='')
    {
        if(empty($select)){
            $select = '*';
        }
        $this->db->select($select);
        $this->db->from($this->table);
        if(is_array($where_data)){
            foreach ($where_data as $key => $value){
                $this->db->where($key,$value);
            }
        }
        if($order_by && is_array($order_by)){
            foreach ($order_by as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
        if(!$count_flag){
            $this->db->limit($page, $offset);
            $query = $this->db->get();
            $result = $query->result();
            return $result;
        }else{
            return  $this->db->count_all_results();
        }
    }

    public function getDataByParam($param = null){
        if($param){
            $this->db->where($param);
        }
        $result = $this->db->get($this->table)->result_array();
        return $result;
    }

    public function findBy($param, $order_field='', $order_by = 'asc'){
        if($param){
            $this->db->where($param);
        }

        if($order_field){
            $this->db->order_by($order_field, $order_by);
        }
        $result = $this->db->get($this->table)->result_array();
        return $result;
    }

    public function getOneByParam($param){
        if($param){
            $this->db->where($param);
        }
        return $this->db->get($this->table)->row_array();
    }

    function getFromId($_id){

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where($this->primary_key, $_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get($id,$key=''){
        if($key==''){
            $key = $this->primary_key;
        }
        $this->db->where($key,$id);
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        }
        return array();
    }

    /**
     * @param $data
     * @param $key
     */
    function register($data,$key='')
    {
        if(empty($key)) $key = $this->primary_key;
        $row = $this->get($data[$key]);
        if(!empty($row)){
            $this->edit($data,$key);
        }else{
            $this->add($data);
        }
    }

    /**
     * @param $data
     * @param $key
     * @return bool
     */
    function edit($data,$key='')
    {
        if(empty($key)) $key = $this->primary_key;
        $query = $this->db->set($data)
            ->where($key, $data[$key])
            ->update($this->table);
        return $query;
    }
    function update($data,$key='')
    {
        if(empty($key)) $key = $this->primary_key;
        $query = $this->db->set($data)
            ->where($key, $data[$key])
            ->update($this->table);
        return $query;
    }


    /**
     * @param $data
     * @return mixed
     */
    function add($data)
    {
        $this->db->trans_start();
        $this->db->insert($this->table, $data);

        $insert_id = $this->db->insert_id();

        $this->db->trans_complete();
        return $insert_id;
    }

    /**
     * @param $data
     * @return mixed
     *
     * 21//08/21 add By Kastumoto
     *
     */
    function insertRecord($data, $is_create_date = true)
    {
        if ($is_create_date){
            $data['create_date'] = date('Y-m-d H:i:s');
            $data['update_date'] = date('Y-m-d H:i:s');
        }

        $this->db->trans_start();
        $this->db->insert($this->table, $data);

        $insert_id = $this->db->insert_id();

        $this->db->trans_complete();
        return $insert_id;
    }
    /**
     * @param $data
     * @param $key
     * @return bool
     *
     * 21//08/21 add By Kastumoto
     */
    function updateRecord($data, $key='',  $is_upd_date = true)
    {
		if($is_upd_date) $data['update_date'] = date('Y-m-d H:i:s');

        if(empty($key)) $key = $this->primary_key;
        $query = $this->db->set($data)
            ->where($key, $data[$key])
            ->update($this->table);
        return $query;
    }

    /**
     * @param $value
     * @param $key
     * @return bool
     */
    function delete( $value , $key='id')
    {
        $this->db->set('visible', 0);
        $this->db->where($key, $value);
        $this->db->update($this->table);
        return true;
    }
    function delete_force( $value , $key='id')
    {
        $this->db->where($key, $value);
        $this->db->delete($this->table);
        return true;
    }
    public function getTwoKey($data,$key1,$key2){
        if(empty($key1) || empty($key2) ){
            return;
        }
        $this->db->where($key2,$data[$key1]);
        $this->db->where($key2,$data[$key2]);
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        }
        return array();
    }

    function editTwokey($data,$key1,$key2)
    {
        $query = $this->db->set($data)
            ->where($key1, $data[$key1])
            ->where($key2, $data[$key2])
            ->update($this->table);
        return $query;
    }

    function registerTwoKey($data,$key1,$key2)
    {
        if(empty($key1) || empty($key2)) return;
        if(empty($data[$key1]) || empty($data[$key2])) return;
        $row = $this->getTwoKey($data,$key1,$key2);
        if(!empty($row)){
            return $this->editTwoKey($data,$key1,$key2);
        }else{
            return $this->add($data);
        }
    }
}