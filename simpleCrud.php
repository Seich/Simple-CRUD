<?php

/*
    Todo: add set and get methods to have a custom number of columns. to be handled.
*/
class simpleCRUD {
    
    public static $dbuser;

    public static $dbpass;

    public static $dbhost;

    public static $dbname;

    private $lastcall;

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

    /*------------------------------------------------
        Overloaded Stuff
    -------------------------------------*/

    public function __call($name, $args) {

    }

    public static function __callStatic($name, $args) {
        $classname = get_called_class();
        
        switch ($name) {
            //class::find(id);
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

    public function __get($name) {
        if(array_key_exists($name, $this->columns)) {
            return $this->columns[$name];
        } else {
            throw new CRUD_exception('Trying to access an undefined property.');
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
        $query = 'INSERT INTO ' .  $table . ' (';
        $query .= implode(', ', array_keys($columns)); //Get's the array keys and combines them using comas.
        $query .= ') value (:';
        $query .= implode(', :', array_keys($columns));
        $query .= ')';

        return $query;
    }

    public static function buildFindQuery($table) {
        $query = 'SELECT * FROM ' . $table . ' WHERE id=:id';
        return $query;
    }

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

}