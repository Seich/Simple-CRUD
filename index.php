<?php
error_reporting(E_ALL);

require 'simpleCrud.php';
require 'entry.php';

simpleCRUD::settings(array(
    'dbname' => 'crud',
    'dbhost' => 'localhost',
    'dbuser' => 'root',
    'dbpass' => ''
));

/*
$entry = new entry();
$entry->title = "Hello World 4";
$entry->body = "Lorem ipsum dolor sit amet?";
$entry->date_published = date('Y-m-d');
$entry->save();
*/


$entry = entry::find(3);
$entry->title = "Bye Bye";
$entry->body = "lol?";
$entry->save();


echo("<pre>");
var_dump($entry);
?>