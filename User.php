<?php
namespace cms\core;
include "Init.php";

class User {

    private $db;
    private $sql;


    /**
     * User constructor.
     * @param Db $db
     */
    public function __construct(Db $db) // dependency injection
    {
        $this->db = $db;
        return $this->db;
    }


    public function all()
    {


    }


    public function get_data( $user_id )
    {

    }

    public function add( Array $user_idata )
    {
        
    }


    public function edit( $user_id )
    {
        
    }

    public function remove( $user_id )
    {
        
    }


}