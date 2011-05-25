<?php
/**
 *  SimpleCRUD, a very tiny and simple CRUD implementation in PHP.
 *
 *  @author Sergio Díaz <seich@martianwabbit.com>
 *  @version 1.0
 *  @package SimpleCRUD
 */

/**
 *  You need to include both the simpleCRUD class and your 'model'. 
 *  You could also set the 'model' at the top of your document.
 */
require 'simpleCrud.php';
require 'entry.php';

/**
 *  This static method is used to set the appropiate database settings.
 */
simpleCRUD::settings(array(
    'dbname' => 'crud',
    'dbhost' => 'localhost',
    'dbuser' => 'root',
    'dbpass' => ''
));

/**
 * The following are examples of how to use all of the CRUD functionality.
 */

//Create
$entry = new entry();
$entry->title = "Hello World";
$entry->body = "Lorem ipsum dolor sit amet?";
$entry->date_published = date('Y-m-d');
$entry->save();

//Read
$entry = entry::find(3);
echo($entry->title); //Hello World
echo($entry->body); //Lorem Ipsum dolor sit amet?;

//Update
$entry = entry::find(3);
$entry->title = "Bye Bye";
$entry->body = "lol?";
$entry->save();

//Delete
$entry = entry::find(7);
$entry->delete();

?>