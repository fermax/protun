<?php
/**
 * Author: HocineFR
 * Date:   30-12-2016
 * Time:   22:12
 */
namespace hocine\protun\core\user;

use hocine\protun\core\database\db;

require_once "init.php";

class User
{
    private $_salt = '$[[158#~^\@@]}}]';
    private $db;
    //hash("sha512", $pass);


    /**
     * User constructor.
     * @param db $db
     */
    public function __construct(db $db)
    {
        $this->db = $db;
        return $this->db;
    }

    public function login($username, $password, $userTable = 'users')
    {
        $query = $this->db->select( $userTable."username, ".$userTable.".password" )
            ->from( $userTable )
            ->where( $userTable.".username", "=" , $username )
            ->and_where( $userTable.".password","=", hash("sha512",trim($password).$this->_salt ) );
        if( $query->rowCount() > 0 )
        {
            return true;
        }
        else
        {
            $this->db->setError("اسم المستخدم أو كلمة المرور غير صحيحة");
            return false;
        }
    }

    /************************************ admin section ***************************/


    /**
     * @param string $userTable
     * @param string $orderBy
     * @return array|null
     */
    public function getAllUsers( $userTable = 'users' , $orderBy = 'id')
    {
        $result = $this->db->select()->from($userTable)->order_by( $orderBy, "DESC" )->all();
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
    public function single($id)
    {
        $result = $this->db->select()->from("users")->where("id", "=", $id)->first();
        if( is_null( $result ) )
        {
            return $this->db->getErrors();
        }
        return $result;

    }



    /**
     * @param array $userData
     * @return array|null|string
     */
    public function createUser(Array $userData)
    {
        // Hashing user Password after sending it to db
        foreach ($userData as $k => $v)
        {
            if( $k == "password" )
            {
                $userData[$k] = hash("sha512", $v.$this->_salt);
            }
        }
        // هل المستخدم موجود مسبقا في قاعدة البيانات؟
        $user_exist = $this->db
                            ->select("username, email")
                            ->from('users')
                            ->where("username", "=", $userData['username'])->rowCount();

        if( $user_exist === 0 )
        {
            $result = $this->db->insert('users', $userData)->execute();
            if( is_null( $result ) )
            {
                return $this->db->getErrors();
            }
            return $result;
        }
        else
        {
            $this->db->setError("This User already exist ! ");
            return $this->db->getErrors();
        }

    }// end method


    /**
     * @param $id
     * @param array $userData
     * @return array|null|string
     */
    public function editUser($id, Array $userData)
    {
        // Hashing user Password after sending it to db
        foreach ($userData as $k => $v)
        {
            if( $k == "password" )
            {
                $userData[$k] = hash("sha512", $v.$this->_salt);
            }
        }
        $result = $this->db->update('users', $userData)->where("id", "=", $id)->execute();
        if( is_null( $result ) )
        {
            return $this->db->getErrors();
        }
        return $result;
    }




    /**
     * @param $id
     * @return array|null|string
     */
    public function deleteUser($id)
    {
        $result = $this->db->delete('users')->where('id', '=', $id)->execute();
        if( is_null( $result ) )
        {
            return $this->db->getErrors();
        }
        return $result;
    }

}
