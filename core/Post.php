<?php
/**
 * Created by PhpStorm.
 * User: HocineFR
 * Date: 31-12-2016
 * Time: 19:37
 */

namespace hocine\protun\core;

class Post
{
    private $db;
    private $tableName     = 'users';
    private $uniqueColumn  = 'username';
    private $idColumn      = 'id';
    private $orderByColumn = 'created_at';


    function __construct(db $db)
    {
        $this->db = $db;
        return $this->db;
    }


    /**
     * @param int $limit
     * @param int $start
     * @return array|null
     */
    public function getAllPosts($limit = 10, $start = 0)
    {
        $result = $this->db->get( $this->tableName, $this->orderByColumn, $limit, $start );

		if ( is_null( $result ) )
        {
	   $this->db->setError( Message::$postNotFound );
            return $this->db->getErrors();
        }
        return $result;
    }

	
    /**
     * @param $id
     * @return array|mixed|null
     */
    public function singlePost($id )
    {
        $result = $this->db->select()->from( $this->tableName )->where( $this->idColumn,"=", $id )->first(true);
        if  ( is_null( $result ) )
        {
            return $this->db->getErrors();
        }

        return $result;

    }


    /**
     * @param $id
     * @param array $data
     * @return array|bool
     */
    public function editPost($id, Array $data )
    {

        $this->db->update( $this->tableName, $data )->where( $this->idColumn, "=", $id )->execute(true);
        if ( count( $this->db->getErrors() ) > 0 )
        {
            $this->db->setError( Message::$editFailure );
            return $this->db->getErrors();
        }
        return true;
    }


    /**
     * @param array $postData
     * @return array|bool
     */
    public function createPost(Array $postData )
    {
        $this->db->insert( $this->tableName, $postData )->execute(true);
        if ( count( $this->db->getErrors() ) > 0 )
        {
            $this->db->setError( Message::$createFailure );
            return $this->db->getErrors();
        }
        return true;
    }




    /**
     * @param $id
     * @return array|bool
     */
    public function deletePost($id )
    {
        $this->db->delete( $this->tableName )->where( $this->idColumn,'=', $id )->execute(true);
        if ( count( $this->db->getErrors() ) > 0 )
        {
            $this->db->setError( Message::$deleteFailure );
            return $this->db->getErrors();
        }
        return true;
    }



    public function tableName( $tableName )
    {
        $this->tableName = $tableName;
    }



    public function setUniqueColumn( $uniqueColumn )
    {
        $this->uniqueColumn = $uniqueColumn;
    }



    public function setOrderByColumn( $orderBy_Column )
    {
        $this->orderByColumn = $orderBy_Column;
    }

    /**
     * @param string $idColumn
     */
    public function setIdColumn(string $idColumn)
    {
        $this->idColumn = $idColumn;
    }


}
