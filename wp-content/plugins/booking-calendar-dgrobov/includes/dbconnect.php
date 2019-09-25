<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
//EDIT ONLY FOLLOWING 5 LINES
$db_host = 'localhost'; //hostname
$db_user = 'zombie'; // username
$db_password = 'katiakurmis'; // password
$db_name = 'zombiebunker'; //database name
$baseDir = '/wp-content/plugins/booking-calendar-dgrobov/'; // Don't change this variable if you will be using booking in the ROOT of the username. 
// otherwise - change to $baseDir = "/directoryName/"; WITH TRAILING SLASH!
$demo = false;
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

$mysqli->query("SET NAMES utf8") or die("err: " . $mysqli->error());
