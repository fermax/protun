<?php
namespace cms\core;
include "Init.php";

class Post {

    private $db;
    private $sql;
    private $result;


    /**
     * User constructor.
     * @param Db $db
     */
    public function __construct(Db $db)
    {
        $this->db = $db;
        return $this->db;
    }


    /**
     * @param int $limit
     * @param String $table_name
     * @param int $offset
     * @return array|null
     */
    public function all($table_name, $limit = 15, $offset = 0)
    {
        $this->sql = $this->db->select()
						  ->from($table_name)
						  ->order_by("id", "DESC")
						  ->limit($limit, $offset);
						  
        $this->result = $this->db->get($this->sql);

        if (null != $this->result )
        {
            return $this->result;
        }
        else
        {
            return null;
        }
    }

    /**
     * @param $id
     * @param String $table
     * @return mixed|null
     */
    public function single($table, $id)
    {
        $this->sql = $this->db->select()
							  ->from($table)
							  ->where("id","=", $id);
							  
        $this->result = $this->db->first($this->sql);
        if ( null != $this->result )
        {
            return $this->result;
        }

        return null;
    }


    /**
     * @param $table
     * @param array $postArray
     * @return string
     */
    public function add($table , Array $postArray)
    {
        return $this->db->insert($table, $postArray);
    }


    /**
     * @param $table
     * @param $id
     * @param array $arrayValues
     * @return bool
     */
    public function edit($table, $id, Array $arrayValues, $condition = "=")
    {
        $query = $this->db->update($table)->set($arrayValues)->where("id", $condition, $id);
        $this->db->get($query);
        //echo $this->db->getQueryString();
        return true;

    }


    /**
     * @param $table_name
     * @param $id
     * @return bool|null
     */
    public function remove($table_name, $id)
    {
        if(is_int($id) && $id > 0)
        {
            $query = $this->db->delete($table_name)->where("id", "=", $id);
            $this->db->get($query);
            //echo $this->db->getQueryString();
            return true;
        }

        return null;
    }

	
}