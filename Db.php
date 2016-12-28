<?php

class db {

    private $host;
    private $user;
    private $pass;
    private $db;
    private $pdo;
    private $query = "";
    private $w   = 0;
    private $error = [];
    private $conditions = [];


    /**
     * Db constructor.
     * @param $host
     * @param $user
     * @param $pass
     * @param $db
     */

    public function __construct( $host = 'localhost', $user = 'root', $pass = '', $db = 'db' )
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
                $statement = $this->pdo->prepare( $this->query );
                // 01- is 'Where' method called ?
                if( $this->w > 0 ){
                       // Prevent SQL Injection
                        $statement->execute($this->conditions);
                        return $statement->fetchAll(PDO::FETCH_OBJ);
                }
                else
                {
                 //02-  No...
                    $statement->execute();
                    return $statement->fetchAll(PDO::FETCH_OBJ);
                }
            }
            catch ( PDOException $e )
            {
                $this->setError( $e->getMessage() );
                return null;
            }

        }
        else if( is_string($query) )
        {
            // يُنصحُ بعدم استعمال هذا الخيار لخطورته ، لأنه غير محمي من ثغرة حقن قاعدة البيانات
            try
            {
                return $this->pdo->query($query)->fetchAll(PDO::FETCH_OBJ);
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
                $statement = $this->pdo->prepare( $this->query );
                // 01- is 'Where' method called ?
                if( $this->w > 0 ){
                    // Prevent SQL Injection
                    $statement->execute($this->conditions);
                    return $statement->fetch(PDO::FETCH_OBJ);
                }
                else
                {
                    //02-  No...
                    $statement->execute();
                    return $statement->fetch(PDO::FETCH_OBJ);
                }
			   
            }
            catch (PDOException $e)
            {
                $this->setError($e->getMessage());
                return null;
            }

        }
        else if(is_string($query))
        {
          // يُنصحُ بعدم استعمال هذا الخيار لخطورته ، لأنه غير محمي من ثغرة حقن قاعدة البيانات

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
        return $this->pdo->query($this->query)->rowCount();   
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
     * @param $param1
     * @param $condition
     * @param $param2
     * @return $this|null
     */
	

    public function where($param1, $condition, $param2)
    {
        if( $this->w === 0 )
        {
            $this->conditions[] = $param2;
            $this->query .= " WHERE ".$param1." ".$condition." ?";
            $this->w++;
            return $this;
        }
        return null;

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
            $results = [];
            $results["query"] = $this->query;
            $results["query_params"] = $this->conditions;
            return $results;
        }
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
