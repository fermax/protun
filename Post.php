<?php
namespace cms\core;
include "Init.php";

class Post {

    private $db;
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
     * @param String $column
     * @param int $offset
     * @return array|null
     */
    public function all($table_name, $limit = 15, $offset = 0, $column = 'id')
    {
        $this->db->select()->from($table_name)->order_by($column, "DESC")->limit($limit, $offset);
        $this->result = $this->db->all();
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
     * @param String $tableName
     * @return mixed|null
     */
    public function single($tableName, $id)
    {
        $this->db->select()->from($tableName)->where("id","=", $id);
        $this->result = $this->db->first();
		
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
        $this->db->insert($table, $postArray)->execute();
        if( count( $this->db->getErrors() ) == 0 )
        {
            return true;
        }

        return false;
    }


    /**
     * @param $table
     * @param $id
     * @param array $arrayValues
     * @param string $condition
     * @return bool
     */

    public function edit($table, $id, Array $arrayValues, $condition = "=")
    {
        $this->db->update($table,$arrayValues)->where("id", $condition, $id)->execute();
        if( count( $this->db->getErrors() ) == 0 )
        {
            return true;
        }

        return false;
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
            $this->db->delete($table_name)->where("id", "=", $id)->execute();
            if( count( $this->db->getErrors() ) == 0 )
            {
                return true;
            }
        }
        return null;
    }

}
