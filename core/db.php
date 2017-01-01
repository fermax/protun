<?php
namespace hocine\protun\core;

use PDO;
use PDOException;


class db {

    private static $host;
    private static $user;
    private static $pass;
    private static $db;
    private static $charset;
    private $pdo;
    private $query = "";
    private $error = [];
    private $conditions = [];


    /**
     * db constructor.
     */
    public function __construct()
    {

        try{

            $this->pdo = new PDO("mysql:host=".self::$host.";dbname=".self::$db."", self::$user, self::$pass);
            $this->pdo->exec("set charset ".self::$charset);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->pdo;
        }
        catch (PDOException $e)
        {
            $this->setError($e->getMessage());
            return null;
        }
    }

    public static function addConnection(Array $connection)
    {
        self::$host    = $connection['host'];
        self::$user    = $connection['user'];
        self::$pass    = $connection['pass'];
        self::$db      = $connection['db'];
        self::$charset = $connection['charset'];
    }


    /**
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }


    /**
     * @param $query
     * @return $this
     */
    function query($query)
    {
        $this->query = $query;
        return $this;
    }


    /**
     * @return null|string
     */
    public function execute()
    {
        try{
            if( count($this->conditions) > 0 )
            {
                $statement = $this->pdo->prepare( $this->query );
                $statement->execute($this->conditions);
                $this->conditions = [];
                return $this->pdo->lastInsertId();
            }
            else
            {
                $this->pdo->query($this->query);
                return $this->pdo->lastInsertId();
            }

        }
		catch (PDOException $e)
        {
            $this->setError($e->getMessage());
            return null;
        }
    }


    /**
     * @return array|null
     */
    public function all()
    {
            try
            {
                // Prevent SQL Injection
                $statement = $this->pdo->prepare( $this->query );
                // is 'Where' method called ?
                if( count($this->conditions) > 0 ){
                    // 01- yes...
                    $statement->execute($this->conditions);
                    $this->conditions = [];
                }
                else
                {
                    //02-  No...
                    $statement->execute();
                }

                return $statement->fetchAll(PDO::FETCH_OBJ);
            }
            catch ( PDOException $e )
            {
                $this->setError( $e->getMessage() );
                return null;
            }
    }


    /**
     * @return mixed|null
     */
    public function first()
    {

            try
            {
                $statement = $this->pdo->prepare( $this->query );
                // 01- is 'Where' method called ?
                if( count($this->conditions) > 0 ){
                    // Prevent SQL Injection
                    $statement->execute($this->conditions);
                    $this->conditions = [];
                }
                else
                {
                    //02-  No...
                    $statement->execute();
                }

                return $statement->fetch(PDO::FETCH_OBJ);

            }
            catch (PDOException $e)
            {
                $this->setError($e->getMessage());
                return null;
            }

    }

    /**
     * @return int
     */
    public function rowCount()
    {
        $statement = $this->pdo->prepare( $this->query );
        if ( count( $this->conditions ) > 0 )
        {
            $statement->execute($this->conditions);
            $this->conditions = [];

        }
        else
        {
            $statement->execute();
        }
        return $statement->rowCount();
    }


    public function get($tableName, $orderBy, $limit, $start)
    {
        return $this->select()->from( $tableName )->order_by( $orderBy, "DESC" )->limit( $limit, $start )->all();
    }


//##################################### db query builder #####################################

    /**
     * @param string $selected
     * @return $this
     */

    public function select ($selected = "*")
    {
        $this->query  = "SELECT ".$selected;
        return $this;
    }

    /**
     * @param $tableName
     * @return $this
     */

    public function from ($tableName)
    {
        $this->query .= " FROM ".$tableName;
        return $this;
    }


    /**
     * @param $param1
     * @param $condition
     * @param $param2
     * @return $this|null
     */
    public function where($param1, $condition, $param2)
    {

            $this->conditions[] = $param2;
            $this->query .= " WHERE ".$param1." ".$condition." ?";
            return $this;

    }


    /**
     * @param $param1
     * @param $condition
     * @param $param2
     * @return $this
     */
    public function and_where($param1, $condition, $param2)
    {
        $this->conditions[] = $param2;
        $this->query .= " AND ".$param1." ".$condition." ?";
        return $this;
    }




    /**
     * @param $param1
     * @param $condition
     * @param $param2
     * @return $this
     */
    public function or_where($param1, $condition, $param2)
    {
        $this->conditions[] = $param2;
        $this->query .= " OR ".$param1." ".$condition." ?";
        return $this;
    }

    /**
     * @param $para1
     * @param $para2
     * @return $this
     */

    public function order_by($para1, $para2)
    {
        $this->query .= " ORDER BY ".$para1." ".$para2;
        return $this;
    }



    /**
     * @param $limit
     * @param $offset
     * @return $this
     */
    public function limit($limit, $offset)
    {
        $this->query .= " LIMIT " . $limit."  OFFSET " .$offset;
        return $this;
    }




    /**
     * @return string
     */

    public function getQueryString()
    {
        return $this->query;
    }

    /**
     * @return array|string
     */
    public function getFullQueryString()
    {
        if( count($this->conditions))
        {
			return [ "query" => $this->query, "query_params" => $this->conditions];
        }
        return $this->query;
    }


    /**
     * @param $table
     * @param array $array
     * @return $this
     */
    public function insert($table, Array $array)
    {

        $this->query = "INSERT INTO ".$table;
        $this->query .= " ( ";

        foreach ($array as $k => $v) {
            $this->query .= " $k , ";
        }

        $this->query = trim($this->query, ", ");
        $this->query .= " ) VALUES ( ";

        foreach ($array as $k => $v) {
            $this->query .= " ?, ";
            $this->conditions[] = $v;
        }

        $this->query  = trim($this->query, ", ");
        $this->query .= ")";
        return $this;
    }


    /**
     * @param $tableName
     * @param array $setValues
     * @return $this
     */
    public function update($tableName, Array $setValues )
    {
        $this->query = "UPDATE ".$tableName;
        $i = 0;
        foreach ($setValues as $key => $value)
        {
            if( $i == 0 )
            {
                $this->query .= " SET ".$key." = '".$value."'";
                $i++;
            }
            else
            {
                $this->query .= ", ".$key." = '".$value."'";
            }
        }
        unset($key);
        unset($value);

        return $this;
    }



    /**
     * @param $table_name
     * @return Db
     */

    public function delete($table_name)
    {
        $this->query = " DELETE FROM " . $table_name;
        return $this;

    }




    /**
     * @param $error
     */
    public function setError($error)
    {
        $this->error[] = $error;
    }


    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->error;
    }
}