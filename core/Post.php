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
            return $this->db->getErrors();
        }
        return $result;
    }



    public function getSinglePost()
    {

    }



    public function createSinglePost(  )
    {

    }



    public function editSinglePost(  )
    {

    }


    /**
     * @param $id
     * @return array|bool
     */
    public function deleteSinglePost($id )
    {
        $this->db->delete( $this->tableName )->where( $this->idColumn,'=', $id )->execute();
        if ( count( $this->db->getErrors() ) > 0 )
        {
            return $this->db->getErrors();
        }
        return true;
    }



    public function tableName( $tableName )
    {
        $this->tableName = $tableName;
    }



    public function uniqueColumn( $uniqueColumn )
    {
        $this->uniqueColumn = $uniqueColumn;
    }



    public function orderByColumn( $orderBy_Column )
    {
        $this->orderByColumn = $orderBy_Column;
    }




}