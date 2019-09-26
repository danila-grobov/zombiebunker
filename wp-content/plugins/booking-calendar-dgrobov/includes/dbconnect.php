<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
//EDIT ONLY FOLLOWING 5 LINES
$db_host = 'localhost'; //hostname
$db_user = 'root'; // username
$db_password = 'root'; // password
$db_name = 'local'; //database name
$baseDir = '/wp-content/plugins/booking-calendar-dgrobov/'; // Don't change this variable if you will be using booking in the ROOT of the username. 
// otherwise - change to $baseDir = "/directoryName/"; WITH TRAILING SLASH!
$demo = false;
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

$mysqli->query("SET NAMES utf8") or die("err: " . $mysqli->error());
