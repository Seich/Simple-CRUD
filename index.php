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
$entry->title = "Hello World 2";
$entry->body = "Lorem ipsum dolor sit amet?";
$entry->date_published = date('Y-m-d');
$entry->save();
*/


$entry = entry::find(3);
$entry->title = "Hello World 3";
$entry->save();


echo("<pre>");
var_dump($entry);
?>