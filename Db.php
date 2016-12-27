<?php
namespace cms\core;
include "Init.php";
use PDO;
use PDOException;

class Db {

    private $host;
    private $user;
    private $pass;
    private $db;
    private $pdo;
    private $query = "";
    private $error = [];


    /**
     * Db constructor.
     * @param $host
     * @param $user
     * @param $pass
     * @param $db
     */

    public function __construct($host, $user, $pass, $db)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->db   = $db;

        try{

            $this->pdo = new PDO("mysql:host=".$this->host.";dbname=".$this->db."", $this->user, $this->pass);
            $this->pdo->exec("set charset utf8");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->pdo;
        }
        catch (PDOException $e)
        {
            $this->setError($e->getMessage());
            return null;
        }
    }


     /**
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }


    /**
     * @param string $query
     * @return array|null
     */

    public function get($query = "")
    {
        if( $query instanceof Db )
        {
            try
            {
                return $this->pdo->query($this->query)->fetchAll(PDO::FETCH_OBJ);
            }
            catch ( PDOException $e )
            {
                $this->setError( $e->getMessage() );
                return null;
            }

        }
        else if( is_string($query) )
        {

            try
            {
                return $this->pdo->query($query)->fetchAll();
            }
            catch (PDOException $e)
            {
                $this->setError($e->getMessage());
                return null;
            }
        }
        else
        {
            $this->setError("We have an error in the parameter of get() method !");
            return null;
        }

    }

    /**
     * @param string $query
     * @return mixed|null
     */

    public function first($query = "")
    {
        if($query instanceof Db)
        {
            try
            {
                return $this->pdo->query($this->query)->fetch(PDO::FETCH_OBJ);
            }
            catch (PDOException $e)
            {
                $this->setError($e->getMessage());
                return null;
            }

        }
        else if(is_string($query))
        {

            try
            {
                return $this->pdo->query($query)->fetch(PDO::FETCH_OBJ);
            }
            catch (PDOException $e)
            {
                $this->setError($e->getMessage());
                return null;
            }
        }
        else
        {
            $this->setError("We have an error in the parameter of first() method !");
            return null;
        }
    }




    /**
     * @param $query
     * @return int
     */
    public function num_rows($query)
    {
        $this->query = $query;
        $q = $this->pdo->query($this->query);
        return $q->rowCount();
    }


//##################################### db query building #####################################

    /**
     * @param string $selected
     * @return $this
     */

    public function select ($selected = "*")
    {
        $this->query .= "SELECT ".$selected;
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
     * @param $if
     * @param $operator
     * @param $sif
     * @return string
     */

    public function where ($if , $operator, $sif)
    {
        $this->query .= " WHERE ".$if." ".$operator." " .$sif;
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
     * @param $table
     * @param array $array
     * @return string
     */
    public function insert($table, Array $array)
    {

        $this->query = "INSERT INTO ".$table;
        $this->query .= " ( ";

        foreach ($array as $k => $v) {
            $this->query .= $k . ", ";
        }

        $this->query = trim($this->query, ", ");
        $this->query .= " ) VALUES ( ";

        foreach ($array as $k => $v) {
            $this->query .= $v . ", ";
        }

        $this->query  = trim($this->query, ", ");
        $this->query .= ")";


        $this->pdo->query($this->query);
        return $this->pdo->lastInsertId();

    }




    /**
     * @param $table
     * @return $this
     */
    public function update($table )
    {
        $this->query = "UPDATE ".$table;
        return $this;
    }




    /**
     * @param array $setValues
     * @return $this
     */
    public function set(Array $setValues)
    {
        $i = 0;
        foreach ($setValues as $key => $value)
        {

            if( $i == 0 )
            {
                $this->query .= " SET ".$key." = ".$value;
                $i++;
            }
            else
            {
                $this->query .= ", ".$key." = ".$value;
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
