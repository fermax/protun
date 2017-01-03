<?php
/**
 * Created by PhpStorm.
 * User: HocineFR
 * Date: 30-12-2016
 * Time: 22:12
 */

namespace hocine\protun\core;
use Rych\Random\Random;

class User
{

    private $db;
    private $userTable     = 'user';
    private $uniqueColumn  = 'username';
    private $idColumn      = 'id';
    private $orderByColumn = 'created_at';

    /**
     * User constructor.
     * @param db $db
     */
    public function __construct(db $db)
    {
        $this->db = $db;
        return $this->db;
    }

    public function login( $username, $password, $userTable = 'users' )
    {
        $query = $this->db->select( "username, password" )
                          ->from( $userTable )
                          ->where( "username", "=" , $username )
                          ->and_where( "password","=", Hash::Make( $password ) );

        if( $query->rowCount( true ) > 0 ) // true: empty conditions array
        {
            return true;
        }
        else
        {
            $this->db->setError( "اسم المستخدم أو كلمة المرور غير صحيحة" );
            return false;
        }
    }

    /************************************ admin section ***************************/

    public function getAllUsers()
    {
        $result =  $this->db->select()
                            ->from( $this->userTable )
                            ->order_by( $this->orderByColumn, "DESC" )
                            ->all( true );

        if( is_null( $result ) )
        {
            return $this->db->getErrors();
        }
        return $result;
    }


    /**
     * @param $id
     * @return array|mixed|null
     */
    public function find($id)
    {
        $result = $this->db->select()->from( $this->userTable )->where( $this->idColumn, "=", $id )->first( true );
        if( $result )
        {
            return $result;
        }

        $this->db->setError("لا يوجد مستخدم");
        return $this->db->getErrors();

    }



    /**
     * @param array $userData
     * @return array|null|string
     */
    public function create( Array $userData )
    {
        // Hashing user Password after sending it to db
        foreach ($userData as $k => $v)
        {
            if( $k == "password" )
            {
                $userData[$k] = Hash::Make( $v );
            }
            elseif ($k == "secret_hash")
            {
                $randomStr = new Random();
                $userData[$k] = $randomStr->getRandomString(128);
            }
        }
        // هل المستخدم موجود مسبقا في قاعدة البيانات؟
        $user_exist = $this->db
                            ->select( $this->uniqueColumn )
                            ->from( $this->userTable )
                            ->where( $this->uniqueColumn, "=", $userData['username'])
                            ->rowCount();

        if( $user_exist === 0 )
        {
            $result = $this->db->insert('users', $userData)->execute( true );
            if( is_null( $result ) )
            {
                $this->db->setError( Message::$createFailure );
                return $this->db->getErrors();
            }
            return $result;
        }
        else
        {
            $this->db->setError("This User already exist ! ");
            $this->db->setError(" Please check your Password length ( must be " . Hash::LENGTH . " chars or greater  ! ) ");
            return $this->db->getErrors();
        }

    }// end method


    /**
     * @param $id
     * @param array $userData
     * @return array|null|string
     */
    public function edit($id, Array $userData)
    {
        // Hashing user Password after sending it to db
        foreach ($userData as $k => $v)
        {
            if( $k == "password" )
            {
                $userData[$k] = Hash::Make($v);
            }
        }
        $result = $this->db->update( $this->userTable, $userData)->where( $this->idColumn, "=", $id)->execute( true );
        if( is_null( $result ) )
        {
            $this->db->setError( Message::$editFailure );
            return $this->db->getErrors();
        }
        return $result;
    }




    /**
     * @param $id
     * @return array|null|string
     */
    public function remove($id)
    {
        $result = $this->db->delete( $this->userTable )->where( $this->idColumn, '=', $id)->execute();
        if( is_null( $result ) )
        {
            $this->db->setError( Message::$deleteFailure );
            return $this->db->getErrors();
        }
        return $result;
    }


    /**
     * @param string $userTable
     */
    public function setUserTable( $userTable)
    {
        $this->userTable = $userTable;
    }


    /**
     * @param string $idColumn
     */
    public function setIdColumn( $idColumn)
    {
        $this->idColumn = $idColumn;
    }


    /**
     * @param string $uniqueColumn
     */
    public function setUniqueColumn( $uniqueColumn)
    {
        $this->uniqueColumn = $uniqueColumn;
    }


    /**
     * @param string $orderByColumn
     */
    public function setOrderByColumn( $orderByColumn)
    {
        $this->orderByColumn = $orderByColumn;
    }

}
