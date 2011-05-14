<?php
require 'simpleCrud.php';
require 'entry.php';

simpleCRUD::settings(array(
    'dbname' => 'crud',
    'dbhost' => 'localhost',
    'dbuser' => 'root',
    'dbpass' => ''
));

$entry = new entry();
$entry->title = "Hello World, I am Mastah Seich<br>";
$entry->body = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce metus mauris, dictum sit amet fermentum vel, vehicula quis arcu. Nam semper laoreet lacus, id lacinia nunc tincidunt sit amet. Curabitur sed placerat massa. Curabitur nunc lectus, dignissim sed suscipit ut, semper in velit. Duis vehicula cursus cursus. Maecenas vitae lacus nibh, in dapibus diam. Nam varius neque id sem ultricies sodales. Nam ac magna lacus. In vel diam augue. Aliquam ultricies, massa nec suscipit pellentesque, ipsum nisi dapibus urna, sed euismod nisl ante in nunc. Curabitur aliquet scelerisque eleifend. Pellentesque luctus ullamcorper ultricies. Sed eu ipsum sem.<br>";
$entry->date_published = date('d-m-Y');
//$entry->save();
?>