<?php

/*
    Todo: add set and get methods to have a custom number of columns. to be handled.
*/
class simpleCRUD {
    
    public static $dbuser;

    public static $dbpass;

    public static $dbhost;

    public static $dbname;

    public $table;

    private $pdo;

    private $pdoStatement;

    private $columns = array();
    
    public function __construct() {
        if($this->pdo == null) {
            $this->connect();
        }
    }

    public static function settings(array $settings) {
        self::$dbuser = $settings['dbuser'];
        self::$dbpass = $settings['dbpass'];
        self::$dbhost = $settings['dbhost'];
        self::$dbname = $settings['dbname'];
    }

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

    public function save() {
        $query = simpleBuilder::buildInsert($this->table, $this->columns);

        $this->pdoStatement = $this->pdo->prepare($query);
        return $this->pdoStatement->execute($this->columns);
    }

    /*------------------------------------------------
        Overloaded Stuff
    -------------------------------------*/

    public function __get($name) {
        if(array_key_exists($name, $this->columns)) {
            return $this->columns[$name];
        } else {
            throw new CRUD_exception("Trying to access an undefined property.");
        }
    }

    public function __set($name, $value) {
        $this->columns[$name] = $value;
    }

    public function __isset($name) {
        return isset($this->columns[$name]);
    }

    public function __unset($name) {
        unset($this->columns[$name]);
    }

}

class CRUD_exception extends Exception {}

class simpleBuilder {
    
   public static function buildInsert($table, $columns) {
        $query = "INSERT INTO $table (";
        $query .= implode(", ", array_keys($columns)); //Get's the array keys and combines them using comas.
        $query .= ") value (:";
        $query .= implode(", :", array_keys($columns));
        $query .= ")";

        return $query;
    }

}