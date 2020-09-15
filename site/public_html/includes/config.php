<?php
$domain = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

$dbServer = getenv('DB_HOST');
$dbUser = getenv('DB_USER');
$dbPw = getenv('DB_PASS');
$dbName = getenv('DB_NAME');
$rpp = 16;

$conn = new mysqli($dbServer, $dbUser, $dbPw, $dbName);

if($conn->connect_error){
  die("Connection Failed!".$conn->connect_error);
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);