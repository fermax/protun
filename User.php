<?php
/**
* Author: HocineFR
* Date: 26-12-2016
* Time: 22:30
*/

class User extends db
{
	
    private $_salt = '$[[158#~^\@@]}}]';
    
    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function login($username, $password)
    {
        $query = $this->select( "users.username, users.password" )
                      ->from( "users" )
                      ->where( "users.username", "=" , $username )
                      ->and_where( "users.password","=", hash("sha512",trim($password).$this->_salt ) );
        if( $query->rowCount() > 0 )
        {
            return true;
        }
        else
        {
            $this->setError("اسم المستخدم أو كلمة المرور غير صحيحة");
            return false;
        }
    }

/************************************ admin section ***************************/


    /**
     * @return array|null
     */
    public function getAllUsers()
    {
        $result = $this->query("SELECT * FROM `users`")->order_by("id", "DESC")->all();
        if( is_null( $result ) )
        {
            return $this->getErrors();
        }
        return $result;
    }


    /**
     * @param $id
     * @return array|mixed|null
     */
    public function single($id)
    {
         $result = $this->select()->from("users")->where("id", "=", $id)->first();
         if( is_null( $result ) )
         {
             return $this->getErrors();
         }
         return $result;

    }



     /**
     * @param array $userData
     * @param string $uniqueColumn
     * @return array|null|string
     */
    public function createUser(Array $userData, $uniqueColumn = 'username')
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
        $user_exist = $this->select("username")->from('users')->where("username", "=", $userData[$uniqueColumn])->rowCount();

        if( $user_exist === 0 )
        {
            $result = $this->insert('users', $userData)->execute();
            if( is_null( $result ) )
            {
                return $this->getErrors();
            }
            return $result;
        }
        else
        {
            $this->setError("This User already exist ! ");
            return $this->getErrors();
        }

    }

    /**
     * @param $id
     * @param array $userData
     * @return array|null|string
     */
    public function editUser($id, Array $userData)
    {
        $result = $this->update('users', $userData)->where("id", "=", $id)->execute();
        if( is_null( $result ) )
        {
            return $this->getErrors();
        }
        return $result;
    }




    /**
     * @param $id
     * @return array|null|string
     */
    public function deleteUser($id)
    {
        $result = $this->delete('users')->where('id', '=', $id)->execute();
        if( is_null( $result ) )
        {
            return $this->getErrors();
        }
        return $result;
    }
}
