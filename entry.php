<?php
/**
 *  SimpleCRUD, a very tiny and simple CRUD implementation in PHP.
 *
 *  @author Sergio Díaz <seich@martianwabbit.com>
 *  @version 1.0
 *  @package SimpleCRUD
 */

/**
 *  This is an example class. Some people would call it a model. To create a new CRUD object just
 *  copy this and set the appropiate table and name for it.
 *  @package SimpleCRUD
 */
class entry extends simpleCRUD {

    /**
     * This is the constructor, it calls the original constructor and sets the table name.
     */
    public function __construct() {
        parent::__construct();
        $this->table = "entries";
    }

}