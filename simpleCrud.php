<?php
/**
 *  SimpleCRUD, a very tiny and simple CRUD implementation in PHP.
 *
 *  @author Sergio Díaz <seich@martianwabbit.com>
 *  @version 1.0
 *  @package SimpleCRUD
 */

/**
 *  The main class. This one is inherited to create CRUD objects.
 *  
 *  @package SimpleCRUD
 */
class simpleCRUD {
    
    /**
     *  Contains the database's user name.
     *  @access public
     *  @var string
     */
    public static $dbuser;

    /**
     *  Contains the database's password
     *  @access public
     *  @var string
     */
    public static $dbpass;

    /**
     *  Contains the database's host
     *  @access public
     *  @var string
     */
    public static $dbhost;

    /**
     *  Contains the database's name
     *  @access public
     *  @var string
     */
    public static $dbname;

    /**
     *  Contains the last action called on the object.
     *  @access private
     *  @var string
     */    
    private $lastcall;

    /**
     *  Contains the current object's table name.
     *  @access public
     *  @var string
     */
    public $table;

    /**
     *  Contains the PDO instance being used to query the database.
     *  @access private
     *  @var object
     */
    private $pdo;

    /**
     *  Contains the PDO statement instance being used to query the database.
     *  @access private
     *  @var object
     */
    private $pdoStatement;

    /**
     *  Contains all of the overloaded variables. These are used as columns when querying 
     *  the database.
     *  @access private
     *  @var array
     */
    private $columns = array();
    
    /**
     *  The constructor intializes the PDO.
     *  @return null
     */
    public function __construct() {
        if($this->pdo == null) {
            $this->connect();
        }
    }

    /**
     *  This function is used to set up the database.
     *  @return null
     */
    public static function settings(array $settings) {
        self::$dbuser = $settings['dbuser'];
        self::$dbpass = $settings['dbpass'];
        self::$dbhost = $settings['dbhost'];
        self::$dbname = $settings['dbname'];
    }

    /**
     *  This function connects to the database and saves the PDO.
     *  @return null
     */
    protected function connect() {
        try {
            $this->pdo = new PDO(
                'mysql:host='. self::$dbhost .
                ';dbname=' . self::$dbname,
                self::$dbuser, 
                self::$dbpass
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        } catch(PDOException $e) {
            throw new CRUD_exception($e->getMessage());
        }
    }

    /**
     *  This function is called to get the kind of find query to be called.
     *  
     *  @return string
     */
    private function getFindQuery() {
        $query = explode('_', $this->lastcall);
        switch(count($query)) {
            case 2:
                switch($query[1]) {
                    case 'id':
                        return 'find_id';
                        break;
                }
                break;
            
            default://No Special Case, just save.
                return false;
                break;
        }
    }

    /**
     *  This function is called to save the current object to the database.
     *  If the object was created from scratch it is inserted to the database, 
     *  if it was first found and then updated, it updates the appropiate row.
     *
     *  @return boolean
     */
    public function save() {
        $lastcall = $this->getFindQuery();
        switch($lastcall) {
            case 'find_id':
                $query = simpleBuilder::buildUpdateQuery($this->table, $this->columns);
                $this->pdoStatement = $this->pdo->prepare($query);
                $this->lastcall = 'save';
                return $this->pdoStatement->execute($this->columns);
                break;

            default:
                $query = simpleBuilder::buildInsert($this->table, $this->columns);
                $this->pdoStatement = $this->pdo->prepare($query);
                $this->lastcall = 'save';
                return $this->pdoStatement->execute($this->columns);
                break;
        }
    }

    /**
     *  The most common query, it finds the row by it's id and sets the column values to 
     *  the column's array.
     *
     *  @return null
     */
    public function findbyId($id) {
        $query = simpleBuilder::buildFindQuery($this->table);

        $this->pdoStatement = $this->pdo->prepare($query);
        $this->pdoStatement->bindParam(':id', $id);
        $this->pdoStatement->execute();
        $this->pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $result = $this->pdoStatement->fetch();
        
        if(!$result) {
            throw new CRUD_exception('No results found.');    
        }

        foreach ($result as $key => $value) {
            $this->$key = $value;
        }
        $this->lastcall = 'find_id';
        $this->oriId = $id;
    }

    /**
     *  Calls the delete query and deletes the current row from the databse.
     *  
     *  @return null
     */
    public function delete() {
        $query = simpleBuilder::buildDeleteQuery($this->table);
        $this->pdoStatement = $this->pdo->prepare($query);
        $this->pdoStatement->bindParam(':id', $this->columns['id']);
        if($this->pdoStatement->execute()) {
            $this->columns = null;
        }
    }

    /**
     *  This is used for static find method calls. 
     *  
     *  @return null
     */
    public static function __callStatic($name, $args) {
        $classname = get_called_class();
        
        switch ($name) {
            case 'find':
                $do = new $classname();
                $do->findbyId($args[0]);
                return $do;
            break;
            
            default:
                return null;
            break;
        }
    }

    /**
     *  Gets the column data.
     *  
     *  @return string | integer
     */
    public function __get($name) {
        if(array_key_exists($name, $this->columns)) {
            return $this->columns[$name];
        } else {
            throw new CRUD_exception('Trying to access an undefined property.');
        }
    }

    /**
     *  Sets column properties.
     *  
     *  @return null
     */
    public function __set($name, $value) {
        $this->columns[$name] = $value;
    }

    /**
     * Checks if  values are set.
     *  
     *  @return boolean
     */
    public function __isset($name) {
        return isset($this->columns[$name]);
    }

    /**
     *  Unsets column values. 
     *  
     *  @return null
     */
    public function __unset($name) {
        unset($this->columns[$name]);
    }

}

/**
 *  This class is used to handle exceptions.
 *  
 *  @package SimpleCrud
 */
class CRUD_exception extends Exception {}

/**
 *  This class is used to generate queries. The returned queries are to be bound to values by the PDO
 *  
 *  @package SimpleCrud
 */
class simpleBuilder {
    
    /**
     *  Builds a insert query.
     *  
     *  @return string
     */
    public static function buildInsert($table, $columns) {
        $query = 'INSERT INTO ' .  $table . ' (';
        $query .= implode(', ', array_keys($columns));
        $query .= ') value (:';
        $query .= implode(', :', array_keys($columns));
        $query .= ')';

        return $query;
    }

    /**
     *  Builds a select query.
     *  
     *  @return string
     */
    public static function buildFindQuery($table) {
        $query = 'SELECT * FROM ' . $table . ' WHERE id=:id';
        return $query;
    }

    /**
     *  Builds a update query.
     *  
     *  @return string
     */
    public static function buildUpdateQuery($table, $columns) {
        $query = 'UPDATE ' . $table . ' SET ';
        $set = array();

        foreach ($columns as $key => $value) {
            if($key == 'oriId') continue;
            $set[] = $key . ' = :' . $key;
        }

        $query .= implode(', ', $set);
        $query .= ' WHERE id=:oriId';

        return $query;
    }

    /**
     *  Builds an delete query.
     *  
     *  @return string
     */
    public static function buildDeleteQuery($table) {
        $query = 'DELETE FROM ' . $table .' WHERE id=:id';
        return $query;
    }

}